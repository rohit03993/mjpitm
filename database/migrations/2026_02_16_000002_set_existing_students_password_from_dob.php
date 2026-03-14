<?php

use App\Models\Student;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * One-time: set each existing student's password to their date of birth in DDMMYYYY format.
     * No other data is changed. Students can change password after login.
     */
    public function up(): void
    {
        Student::query()
            ->whereNotNull('date_of_birth')
            ->chunkById(100, function ($students) {
                foreach ($students as $student) {
                    $dobPassword = Student::dateOfBirthToPassword($student->date_of_birth);
                    if ($dobPassword) {
                        $student->password = Hash::make($dobPassword);
                        $student->setPlainPassword($dobPassword);
                        $student->saveQuietly();
                    }
                }
            });
    }

    /**
     * Reverse not possible without storing previous passwords.
     */
    public function down(): void
    {
        // No-op: cannot restore previous passwords
    }
};
