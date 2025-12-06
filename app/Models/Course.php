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
        'duration_months',
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

    /**
     * Get formatted duration string (e.g., "2 months", "1 year", "2 years 6 months")
     */
    public function getFormattedDurationAttribute()
    {
        $totalMonths = $this->duration_months ?? 0;
        
        if ($totalMonths == 0) {
            return 'Not specified';
        }
        
        // If less than 12 months, show only months
        if ($totalMonths < 12) {
            return $totalMonths . ' ' . ($totalMonths == 1 ? 'month' : 'months');
        }
        
        // Calculate years and remaining months
        $displayYears = floor($totalMonths / 12);
        $remainingMonths = $totalMonths % 12;
        
        $result = '';
        if ($displayYears > 0) {
            $result = $displayYears . ' ' . ($displayYears == 1 ? 'year' : 'years');
        }
        if ($remainingMonths > 0) {
            if ($result) $result .= ' ';
            $result .= $remainingMonths . ' ' . ($remainingMonths == 1 ? 'month' : 'months');
        }
        
        return $result;
    }

    /**
     * Get total duration in months (alias for duration_months)
     */
    public function getTotalMonthsAttribute()
    {
        return $this->duration_months ?? 0;
    }
}
