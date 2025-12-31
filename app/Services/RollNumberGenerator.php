<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Institute;
use App\Models\CourseCategory;
use Illuminate\Support\Str;

class RollNumberGenerator
{
    /**
     * Generate a unique roll number for a student
     * Format: {INSTITUTE_CODE}{YEAR}{CATEGORY_CODE}{RANDOM_NUMBER}
     * Example: MJPITM202501123456
     * 
     * @param Student $student
     * @return string
     * @throws \Exception
     */
    public static function generate(Student $student): string
    {
        // Get institute code
        $institute = $student->institute;
        if (!$institute) {
            throw new \Exception('Student must have an institute assigned.');
        }

        $instituteCode = $institute->institute_code;
        if (empty($instituteCode)) {
            throw new \Exception('Institute code is not set. Please set institute_code for the institute.');
        }

        // Get current year (year of registration/activation)
        $year = date('Y');

        // Get category code from student's course
        $course = $student->course;
        if (!$course) {
            throw new \Exception('Student must have a course assigned.');
        }

        $category = $course->category;
        if (!$category) {
            throw new \Exception('Course must have a category assigned.');
        }

        $categoryCode = $category->roll_number_code;
        if (empty($categoryCode)) {
            throw new \Exception('Category roll number code is not set. Please set roll_number_code for the category.');
        }

        // Generate unique random number (4-6 digits)
        $randomNumber = static::generateUniqueRandomNumber($instituteCode, $year, $categoryCode);

        // Build roll number: {INSTITUTE_CODE}{YEAR}{CATEGORY_CODE}{RANDOM_NUMBER}
        $rollNumber = $instituteCode . $year . $categoryCode . $randomNumber;

        return $rollNumber;
    }

    /**
     * Generate a unique random number (4-6 digits) that doesn't exist
     * 
     * @param string $instituteCode
     * @param string $year
     * @param string $categoryCode
     * @return string
     */
    protected static function generateUniqueRandomNumber(string $instituteCode, string $year, string $categoryCode): string
    {
        $maxAttempts = 100; // Prevent infinite loop
        $attempt = 0;

        do {
            // Generate random number: 4-6 digits (1000 to 999999)
            $randomNumber = str_pad((string)rand(1000, 999999), 4, '0', STR_PAD_LEFT);
            
            // Build the prefix to check
            $prefix = $instituteCode . $year . $categoryCode;
            
            // Check if this roll number already exists
            $exists = Student::where('roll_number', $prefix . $randomNumber)->exists();
            
            $attempt++;
            
            if ($attempt >= $maxAttempts) {
                // If we can't find a unique number, use timestamp-based approach
                $randomNumber = substr(str_replace('.', '', microtime(true)), -6);
            }
        } while ($exists && $attempt < $maxAttempts);

        return $randomNumber;
    }

    /**
     * Generate roll number for a specific year (used when session changes)
     * Format: {INSTITUTE_CODE}{YEAR}{CATEGORY_CODE}{RANDOM_NUMBER}
     * 
     * @param Student $student
     * @param string $year Year to use in roll number (e.g., "2022")
     * @return string
     * @throws \Exception
     */
    public static function generateForYear(Student $student, string $year): string
    {
        // Get institute code
        $institute = $student->institute;
        if (!$institute) {
            throw new \Exception('Student must have an institute assigned.');
        }

        $instituteCode = $institute->institute_code;
        if (empty($instituteCode)) {
            throw new \Exception('Institute code is not set. Please set institute_code for the institute.');
        }

        // Get category code from student's course
        $course = $student->course;
        if (!$course) {
            throw new \Exception('Student must have a course assigned.');
        }

        $category = $course->category;
        if (!$category) {
            throw new \Exception('Course must have a category assigned.');
        }

        $categoryCode = $category->roll_number_code;
        if (empty($categoryCode)) {
            throw new \Exception('Category roll number code is not set. Please set roll_number_code for the category.');
        }

        // Generate unique random number (4-6 digits) for the specified year
        $randomNumber = static::generateUniqueRandomNumberForYear($instituteCode, $year, $categoryCode, $student->id);

        // Build roll number: {INSTITUTE_CODE}{YEAR}{CATEGORY_CODE}{RANDOM_NUMBER}
        $rollNumber = $instituteCode . $year . $categoryCode . $randomNumber;

        return $rollNumber;
    }

    /**
     * Generate a unique random number for a specific year (excludes current student)
     * 
     * @param string $instituteCode
     * @param string $year
     * @param string $categoryCode
     * @param int|null $excludeStudentId
     * @return string
     */
    protected static function generateUniqueRandomNumberForYear(string $instituteCode, string $year, string $categoryCode, int $excludeStudentId = null): string
    {
        $maxAttempts = 100; // Prevent infinite loop
        $attempt = 0;

        do {
            // Generate random number: 4-6 digits (1000 to 999999)
            $randomNumber = str_pad((string)rand(1000, 999999), 4, '0', STR_PAD_LEFT);
            
            // Build the prefix to check
            $prefix = $instituteCode . $year . $categoryCode;
            
            // Check if this roll number already exists (excluding current student)
            $existsQuery = Student::where('roll_number', $prefix . $randomNumber);
            if ($excludeStudentId) {
                $existsQuery->where('id', '!=', $excludeStudentId);
            }
            $exists = $existsQuery->exists();
            
            $attempt++;
            
            if ($attempt >= $maxAttempts) {
                // If we can't find a unique number, use timestamp-based approach
                $randomNumber = substr(str_replace('.', '', microtime(true)), -6);
            }
        } while ($exists && $attempt < $maxAttempts);

        return $randomNumber;
    }

    /**
     * Validate if a roll number format is correct
     * 
     * @param string $rollNumber
     * @return bool
     */
    public static function validateFormat(string $rollNumber): bool
    {
        // Basic validation: should be alphanumeric and reasonable length
        // Format: {INSTITUTE_CODE}{YEAR}{CATEGORY_CODE}{RANDOM_NUMBER}
        // Minimum: 6 chars (e.g., ABC202401) + 4 digits = 10 chars
        // Maximum: 10 chars (institute) + 4 (year) + 3 (category) + 6 (random) = 23 chars
        return preg_match('/^[A-Z0-9]{10,23}$/i', $rollNumber) === 1;
    }
}
