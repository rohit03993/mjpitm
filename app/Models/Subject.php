<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'code',
        'credits',
        'semester',
        'status',
    ];

    /**
     * Get the course this subject belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get results for this subject
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
