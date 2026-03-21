<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'event',
        'actor_id',
        'changes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'metadata' => 'array',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
