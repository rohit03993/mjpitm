<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'semester',
        'academic_year',
        'date_of_issue',
        'total_subjects',
        'total_marks_obtained',
        'total_max_marks',
        'overall_percentage',
        'overall_grade',
        'status',
        'entered_by',
        'verified_by',
        'verified_at',
        'published_at',
        'remarks',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'total_marks_obtained' => 'decimal:2',
            'total_max_marks' => 'decimal:2',
            'overall_percentage' => 'decimal:2',
            'date_of_issue' => 'date',
            'verified_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the student this result belongs to
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course this result is for
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all subject results for this semester
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get the admin who entered this result
     */
    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * Get the admin who verified this result
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Calculate overall percentage
     */
    public function calculateOverall()
    {
        if ($this->total_max_marks > 0) {
            $this->overall_percentage = ($this->total_marks_obtained / $this->total_max_marks) * 100;
            // Grade calculation removed - not needed
        }
    }

    /**
     * Scope to get only truly published results
     * Ensures results went through the proper publish workflow
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTrulyPublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->whereNotNull('verified_at')
            ->whereNotNull('verified_by')
            ->where('published_at', '<=', now())
            ->where('verified_at', '<=', now())
            ->whereHas('results', function($q) {
                $q->where('status', 'published');
            });
    }

    /**
     * Check if this result is truly published
     * 
     * @return bool
     */
    public function isTrulyPublished()
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->verified_at !== null
            && $this->verified_by !== null
            && $this->published_at <= now()
            && $this->verified_at <= now()
            && $this->results()->where('status', 'published')->exists();
    }
}
