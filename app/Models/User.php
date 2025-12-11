<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is institute admin
     */
    public function isInstituteAdmin(): bool
    {
        return $this->role === 'institute_admin';
    }

    /**
     * Check if user is staff (helper created by Super Admin)
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user can access admin login (Super Admin or Staff)
     */
    public function canAccessAdminLogin(): bool
    {
        return $this->isSuperAdmin() || $this->isStaff();
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
