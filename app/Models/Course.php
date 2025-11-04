<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'name',
        'code',
        'duration_years',
        'description',
        'status',
    ];

    /**
     * Get the institute that owns this course
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
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
