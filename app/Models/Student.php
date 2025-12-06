<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

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
     * Get qualifications for this student
     */
    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }
}
