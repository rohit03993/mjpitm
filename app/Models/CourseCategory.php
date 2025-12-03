<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'name',
        'code',
        'description',
        'display_order',
        'status',
    ];

    /**
     * Get the institute that owns this category
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get courses in this category
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    /**
     * Get active courses in this category
     */
    public function activeCourses()
    {
        return $this->hasMany(Course::class, 'category_id')->where('status', 'active');
    }

    /**
     * Scope to get active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to order by display_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}

