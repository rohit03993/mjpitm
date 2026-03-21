<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'institute_id',
        'course_id',
        'registration_number',
        'roll_number',
        'name',
        'mother_name',
        'father_name',
        'email',
        'phone',
        'father_contact',
        'mother_contact',
        'date_of_birth',
        'gender',
        'category',
        'aadhaar_number',
        'passport_number',
        'is_employed',
        'employer_name',
        'designation',
        'photo',
        'signature',
        'aadhar_front',
        'aadhar_back',
        'certificate_class_10th',
        'certificate_class_12th',
        'certificate_graduation',
        'certificate_others',
        'address',
        'country',
        'nationality',
        'state',
        'district',
        'pin_code',
        'password',
        'password_plain_encrypted',
        'admission_year',
        'session',
        'mode_of_study',
        'admission_type',
        'hostel_facility_required',
        'current_semester',
        'stream',
        'registration_fee',
        'entrance_fee',
        'enrollment_fee',
        'tuition_fee',
        'caution_money',
        'hostel_fee_amount',
        'late_fee',
        'total_deposit',
        'institute_admin_fee',
        'pay_in_installment',
        'payment_mode',
        'bank_account',
        'deposit_date',
        'declaration_accepted',
        'created_by',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'deposit_date' => 'date',
            'password' => 'hashed',
            'is_employed' => 'boolean',
            'hostel_facility_required' => 'boolean',
            'pay_in_installment' => 'boolean',
            'declaration_accepted' => 'boolean',
            'registration_fee' => 'decimal:2',
            'entrance_fee' => 'decimal:2',
            'enrollment_fee' => 'decimal:2',
            'tuition_fee' => 'decimal:2',
            'caution_money' => 'decimal:2',
            'hostel_fee_amount' => 'decimal:2',
            'late_fee' => 'decimal:2',
            'total_deposit' => 'decimal:2',
            'institute_admin_fee' => 'decimal:2',
        ];
    }

    /**
     * Get the institute this student belongs to
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get the course this student is enrolled in
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the admin who created this student
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get fees for this student
     */
    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    /**
     * Get results for this student
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get semester results for this student
     */
    public function semesterResults()
    {
        return $this->hasMany(SemesterResult::class);
    }

    /**
     * Get qualifications for this student
     */
    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }

    /**
     * Audit history of changes made to the student profile.
     */
    public function audits()
    {
        return $this->hasMany(StudentAudit::class)->latest();
    }

    /**
     * Convert date of birth to default password format (DDMMYYYY, e.g. 03091992).
     * Used for new registrations and one-time sync of existing students.
     */
    public static function dateOfBirthToPassword(?string $dateOfBirth): ?string
    {
        if (!$dateOfBirth) {
            return null;
        }
        try {
            return Carbon::parse($dateOfBirth)->format('dmY');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Default login credentials derived from DOB (DDMMYYYY plain → bcrypt + encrypted copy for admin view).
     * Returns null if DOB cannot be parsed.
     */
    public static function passwordCredentialsFromDateOfBirth(string $dateOfBirth): ?array
    {
        $plain = self::dateOfBirthToPassword($dateOfBirth);
        if (! $plain) {
            return null;
        }

        return [
            'password' => Hash::make($plain),
            'password_plain_encrypted' => encrypt($plain),
        ];
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
