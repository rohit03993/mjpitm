<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Institute;
use App\Models\CourseCategory;
use Illuminate\Support\Str;

class RollNumberGenerator
{
    /**
     * Generate a unique enrollment number (roll number) for a student
     * Format: {INSTITUTE_PREFIX}{SESSION_YEAR_2_DIGITS}{STUDENT_NUMBER}
     * Example: MJPITM2001000 (MJPITM + 20 from 2020-21 + 01000 starting from 1000)
     * 
     * @param Student $student
     * @return string
     * @throws \Exception
     */
    public static function generate(Student $student): string
    {
        // Get institute prefix (MJPITM or MJPIPS)
        $institute = $student->institute;
        if (!$institute) {
            throw new \Exception('Student must have an institute assigned.');
        }

        // Determine institute prefix based on institute_id
        $institutePrefix = $institute->id == 1 ? 'MJPITM' : 'MJPIPS';

        // Get 2-digit year from student's session (e.g., "2020-21" -> "20")
        // Session is required, so it should always exist
        if (empty($student->session)) {
            throw new \Exception('Student session is required to generate enrollment number. Please set the student\'s session first.');
        }

        $sessionParts = explode('-', $student->session);
        $fullYear = $sessionParts[0] ?? date('Y'); // Extract year from session (e.g., "2020-21" -> "2020")
        
        // Validate year is numeric and reasonable
        if (!is_numeric($fullYear) || strlen($fullYear) !== 4) {
            throw new \Exception('Invalid session format. Session must be in format YYYY-YY (e.g., 2020-21).');
        }

        // Extract last 2 digits of year (e.g., "2020" -> "20")
        $yearTwoDigits = substr($fullYear, -2);

        // Generate unique sequential student number starting from 1000
        $studentNumber = static::generateUniqueStudentNumber($institutePrefix, $yearTwoDigits, $student->id);

        // Build enrollment number: {INSTITUTE_PREFIX}{YEAR_2_DIGITS}{STUDENT_NUMBER}
        // Example: MJPITM2001000
        $enrollmentNumber = $institutePrefix . $yearTwoDigits . $studentNumber;

        return $enrollmentNumber;
    }

    /**
     * Generate a unique sequential student number starting from 1000
     * Format: 5 digits (01000, 01001, 01002, etc.)
     * 
     * @param string $institutePrefix (MJPITM or MJPIPS)
     * @param string $yearTwoDigits (e.g., "20" from 2020-21)
     * @param int|null $excludeStudentId Student ID to exclude from check (for session changes)
     * @return string
     */
    protected static function generateUniqueStudentNumber(string $institutePrefix, string $yearTwoDigits, ?int $excludeStudentId = null): string
    {
        // Build prefix to search for existing enrollment numbers
        $prefix = $institutePrefix . $yearTwoDigits;
        
        // Get the last enrollment number for this institute and year
        $query = Student::where('roll_number', 'like', $prefix . '%')
            ->whereNotNull('roll_number');
        
        if ($excludeStudentId) {
            $query->where('id', '!=', $excludeStudentId);
        }
        
        $lastStudent = $query->orderBy('roll_number', 'desc')->first();
        
        $nextNumber = 1000; // Start from 1000
        
        if ($lastStudent && $lastStudent->roll_number) {
            // Extract student number from enrollment number
            // Format: MJPITM2001000 -> extract 01000 (last 5 digits)
            $lastEnrollmentNumber = $lastStudent->roll_number;
            
            // Remove prefix to get the number part
            $numberPart = substr($lastEnrollmentNumber, strlen($prefix));
            
            if (is_numeric($numberPart)) {
                $lastNumber = (int)$numberPart;
                // If last number is less than 1000, start from 1000
                // Otherwise, increment from last number
                $nextNumber = max(1000, $lastNumber + 1);
            }
        }
        
        // Ensure uniqueness by checking if the number already exists
        $maxAttempts = 1000; // Allow up to 1000 attempts
        $attempts = 0;
        
        do {
            $studentNumber = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            $enrollmentNumber = $prefix . $studentNumber;
            
            // Check if this enrollment number already exists
            $existsQuery = Student::where('roll_number', $enrollmentNumber);
            if ($excludeStudentId) {
                $existsQuery->where('id', '!=', $excludeStudentId);
            }
            $exists = $existsQuery->exists();
            
            if (!$exists) {
                return $studentNumber;
            }
            
            $nextNumber++;
            $attempts++;
        } while ($attempts < $maxAttempts);
        
        // Fallback: use timestamp-based number if we can't find a unique sequence
        $timestamp = time();
        return substr($timestamp, -5);
    }

    /**
     * Generate enrollment number for a specific year (used when session changes)
     * Format: {INSTITUTE_PREFIX}{SESSION_YEAR_2_DIGITS}{STUDENT_NUMBER}
     * 
     * @param Student $student
     * @param string $year Full year to use (e.g., "2020" from "2020-21")
     * @return string
     * @throws \Exception
     */
    public static function generateForYear(Student $student, string $year): string
    {
        // Get institute prefix (MJPITM or MJPIPS)
        $institute = $student->institute;
        if (!$institute) {
            throw new \Exception('Student must have an institute assigned.');
        }

        // Determine institute prefix based on institute_id
        $institutePrefix = $institute->id == 1 ? 'MJPITM' : 'MJPIPS';

        // Validate year is numeric and reasonable
        if (!is_numeric($year) || strlen($year) !== 4) {
            throw new \Exception('Invalid year format. Year must be 4 digits (e.g., 2020).');
        }

        // Extract last 2 digits of year (e.g., "2020" -> "20")
        $yearTwoDigits = substr($year, -2);

        // Generate unique sequential student number starting from 1000
        $studentNumber = static::generateUniqueStudentNumber($institutePrefix, $yearTwoDigits, $student->id);

        // Build enrollment number: {INSTITUTE_PREFIX}{YEAR_2_DIGITS}{STUDENT_NUMBER}
        // Example: MJPITM2001000
        $enrollmentNumber = $institutePrefix . $yearTwoDigits . $studentNumber;

        return $enrollmentNumber;
    }


    /**
     * Validate if an enrollment number format is correct
     * Format: {INSTITUTE_PREFIX}{SESSION_YEAR_2_DIGITS}{STUDENT_NUMBER}
     * Example: MJPITM2001000 (MJPITM + 20 + 01000)
     * 
     * @param string $enrollmentNumber
     * @return bool
     */
    public static function validateFormat(string $enrollmentNumber): bool
    {
        // Format: MJPITM or MJPIPS (6 chars) + 2 digits (year) + 5 digits (student number) = 13 chars
        // Example: MJPITM2001000
        return preg_match('/^(MJPITM|MJPIPS)\d{7}$/i', $enrollmentNumber) === 1;
    }
}
