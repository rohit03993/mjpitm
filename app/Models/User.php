<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /** Matches `users.role` enum — keep in sync with migrations. */
    public const ROLE_NAMES = ['super_admin', 'institute_admin', 'staff', 'student'];

    /** Max super admin accounts (enforced in SuperAdmin UserController). */
    public const MAX_SUPER_ADMINS = 2;

    public static function superAdminCount(): int
    {
        return static::query()->where('role', 'super_admin')->count();
    }

    public static function canCreateAnotherSuperAdmin(): bool
    {
        return self::superAdminCount() < self::MAX_SUPER_ADMINS;
    }

    /** @var string Guard used by spatie/laravel-permission (must match Role guard). */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_plain_encrypted',
        'role',
        'institute_id',
        'center',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (User $user) {
            if (! $user->role || ! Schema::hasTable('roles')) {
                return;
            }
            if (! in_array($user->role, self::ROLE_NAMES, true)) {
                return;
            }
            $user->syncRoles([$user->role]);
        });
    }

    /**
     * Get the institute that the user belongs to (for admins)
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get students created by this admin
     */
    public function createdStudents()
    {
        return $this->hasMany(Student::class, 'created_by');
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin'
            || $this->hasRoleWhenTablesExist('super_admin');
    }

    /**
     * Check if user is institute admin
     */
    public function isInstituteAdmin(): bool
    {
        return $this->role === 'institute_admin'
            || $this->hasRoleWhenTablesExist('institute_admin');
    }

    /**
     * Check if user is staff (helper created by Super Admin)
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff'
            || $this->hasRoleWhenTablesExist('staff');
    }

    /**
     * Spatie role check only when permission tables exist (avoids errors before migrate).
     */
    protected function hasRoleWhenTablesExist(string $role): bool
    {
        if (! Schema::hasTable('roles')) {
            return false;
        }

        return $this->hasRole($role);
    }

    /**
     * Check if user can access admin login (Super Admin or Staff)
     */
    public function canAccessAdminLogin(): bool
    {
        return $this->isSuperAdmin() || $this->isStaff();
    }

    /**
     * Whether this admin (web guard) may view the given student — mirrors {@see \App\Http\Controllers\Admin\StudentController::show}.
     */
    public function canViewStudentRecord(Student $student): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $instituteId = session('current_institute_id');

        return ! ($student->created_by !== $this->id
            && ($student->created_by !== null || $student->institute_id != $instituteId));
    }

    /**
     * Get the decrypted plain password (only for Super Admin viewing)
     */
    public function getPlainPasswordAttribute(): ?string
    {
        if (!$this->password_plain_encrypted) {
            return null;
        }
        
        try {
            return decrypt($this->password_plain_encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set and encrypt the plain password
     */
    public function setPlainPassword(string $password): void
    {
        $this->password_plain_encrypted = encrypt($password);
    }
}
