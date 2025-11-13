<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'examination',
        'year_of_passing',
        'board_university',
        'percentage',
        'cgpa',
        'subjects',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
        ];
    }

    /**
     * Get the student this qualification belongs to
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

