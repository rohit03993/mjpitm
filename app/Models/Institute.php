<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'description',
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
