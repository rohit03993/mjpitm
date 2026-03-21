<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentAudit;
use Illuminate\Support\Facades\Auth;

class StudentAuditLogger
{
    private const EXCLUDED_FIELDS = [
        'password',
        'password_plain_encrypted',
        'remember_token',
        'updated_at',
        'created_at',
    ];

    public static function logCreated(Student $student): void
    {
        self::createAudit($student, 'created', [
            'after' => self::sanitize($student->getAttributes()),
        ]);
    }

    public static function logUpdated(Student $student, array $oldValues, array $newValues): void
    {
        if (empty($newValues)) {
            return;
        }

        self::createAudit($student, 'updated', [
            'before' => self::sanitize($oldValues),
            'after' => self::sanitize($newValues),
        ]);
    }

    public static function logDeleted(Student $student): void
    {
        self::createAudit($student, 'deleted', [
            'before' => self::sanitize($student->getAttributes()),
        ]);
    }

    public static function logRelatedCreated(Student $student, string $entity, array $after, ?int $relatedId = null): void
    {
        self::createAudit($student, "{$entity}_created", [
            'after' => self::sanitize($after),
        ], [
            'entity' => $entity,
            'related_id' => $relatedId,
        ]);
    }

    public static function logRelatedUpdated(
        Student $student,
        string $entity,
        array $before,
        array $after,
        ?int $relatedId = null
    ): void {
        if (empty($after)) {
            return;
        }

        self::createAudit($student, "{$entity}_updated", [
            'before' => self::sanitize($before),
            'after' => self::sanitize($after),
        ], [
            'entity' => $entity,
            'related_id' => $relatedId,
        ]);
    }

    public static function logRelatedDeleted(Student $student, string $entity, array $before, ?int $relatedId = null): void
    {
        self::createAudit($student, "{$entity}_deleted", [
            'before' => self::sanitize($before),
        ], [
            'entity' => $entity,
            'related_id' => $relatedId,
        ]);
    }

    private static function createAudit(Student $student, string $event, array $changes, array $metadata = []): void
    {
        StudentAudit::create([
            'student_id' => $student->id,
            'event' => $event,
            'actor_id' => Auth::id(),
            'changes' => $changes,
            'metadata' => array_merge([
                'ip' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ], $metadata),
        ]);
    }

    private static function sanitize(array $data): array
    {
        foreach (self::EXCLUDED_FIELDS as $field) {
            unset($data[$field]);
        }

        return $data;
    }
}
