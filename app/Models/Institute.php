<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'institute_code',
        'domain',
        'description',
        'contact_address',
        'contact_email',
        'contact_phone',
        'marksheet_header_logo',
        'marksheet_watermark_image',
        'marksheet_footer_logo_1',
        'marksheet_footer_logo_2',
        'marksheet_footer_logo_3',
        'marksheet_footer_logo_4',
        'status',
    ];

    /**
     * Get courses for this institute
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get course categories for this institute
     */
    public function courseCategories()
    {
        return $this->hasMany(CourseCategory::class);
    }

    /**
     * Get active course categories for this institute (ordered)
     */
    public function activeCourseCategories()
    {
        return $this->hasMany(CourseCategory::class)
            ->where('status', 'active')
            ->orderBy('display_order')
            ->orderBy('name');
    }

    /**
     * Get students for this institute
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get admins for this institute
     */
    public function admins()
    {
        return $this->hasMany(User::class);
    }
}
