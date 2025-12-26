<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'semester_result_id',
        'exam_type',
        'semester',
        'academic_year',
        'marks_obtained',
        'theory_marks_obtained',
        'practical_marks_obtained',
        'total_marks',
        'percentage',
        'grade',
        'status',
        'uploaded_by',
        'verified_by',
        'verified_at',
        'published_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
            'theory_marks_obtained' => 'decimal:2',
            'practical_marks_obtained' => 'decimal:2',
            'total_marks' => 'decimal:2',
            'percentage' => 'decimal:2',
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
     * Get the subject this result is for
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the admin who uploaded this result
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the admin who verified this result
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the semester result this belongs to
     */
    public function semesterResult()
    {
        return $this->belongsTo(SemesterResult::class);
    }

    /**
     * Calculate percentage automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($result) {
            // Auto-calculate marks_obtained from theory + practical if not set
            if ($result->theory_marks_obtained !== null || $result->practical_marks_obtained !== null) {
                $theory = $result->theory_marks_obtained ?? 0;
                $practical = $result->practical_marks_obtained ?? 0;
                $result->marks_obtained = $theory + $practical;
            }
            
            if ($result->marks_obtained && $result->total_marks) {
                $result->percentage = ($result->marks_obtained / $result->total_marks) * 100;
                // Grade calculation removed - not needed
            }
        });
    }
}
