<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'category_id',
        'name',
        'code',
        'duration_years',
        'description',
        'status',
        'registration_fee',
        'entrance_fee',
        'enrollment_fee',
        'tuition_fee',
        'caution_money',
        'hostel_fee_amount',
        'late_fee',
    ];

    protected function casts(): array
    {
        return [
            'registration_fee' => 'decimal:2',
            'entrance_fee' => 'decimal:2',
            'enrollment_fee' => 'decimal:2',
            'tuition_fee' => 'decimal:2',
            'caution_money' => 'decimal:2',
            'hostel_fee_amount' => 'decimal:2',
            'late_fee' => 'decimal:2',
        ];
    }

    /**
     * Get the institute that owns this course
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get the category this course belongs to
     */
    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    /**
     * Get students enrolled in this course
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get subjects for this course
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
