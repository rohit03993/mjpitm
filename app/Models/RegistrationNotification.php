<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'institute_id',
        'registration_type',
        'read_by',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the student that this notification is for
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the institute
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get the admin who read this notification
     */
    public function reader()
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($userId): void
    {
        $this->update([
            'read_by' => $userId,
            'read_at' => now(),
        ]);
    }
}

