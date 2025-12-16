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
        'roll_number_code',
        'description',
        'image',
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

    /**
     * Boot method to auto-generate roll_number_code if not provided
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            // Auto-generate roll_number_code if not provided
            if (empty($category->roll_number_code)) {
                $category->roll_number_code = static::generateRollNumberCode($category->institute_id);
            }
        });
    }

    /**
     * Generate a unique roll number code for a category
     * Format: 2-3 digit number (01, 02, 03, ... 10, 11, ... 100, etc.)
     */
    protected static function generateRollNumberCode($instituteId): string
    {
        // Get all existing roll_number_codes for this institute
        $existingCodes = static::where('institute_id', $instituteId)
            ->whereNotNull('roll_number_code')
            ->pluck('roll_number_code')
            ->map(function ($code) {
                return (int)$code; // Convert to integer
            })
            ->toArray();

        // If no existing codes, start with 01
        if (empty($existingCodes)) {
            return '01';
        }

        // Find the maximum code and increment
        $maxCode = max($existingCodes);
        $nextCode = $maxCode + 1;
        
        // Format: 01-99 as 2 digits, 100+ as 3 digits (but keep minimum 2 digits)
        if ($nextCode <= 99) {
            return str_pad($nextCode, 2, '0', STR_PAD_LEFT);
        } else {
            // For 100+, use 3 digits
            return str_pad($nextCode, 3, '0', STR_PAD_LEFT);
        }
    }
}

