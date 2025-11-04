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
        'roll_number',
        'name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'password',
        'admission_year',
        'current_semester',
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
            'password' => 'hashed',
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
}
