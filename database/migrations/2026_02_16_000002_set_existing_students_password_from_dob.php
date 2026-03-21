<?php

use App\Models\Student;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * One-time: set each existing student's password to their date of birth in DDMMYYYY format.
     * Uses the query builder (not the Student model) so this runs even if soft-deletes column
     * is added in a later migration. No rows are deleted.
     */
    public function up(): void
    {
        DB::table('students')
            ->whereNotNull('date_of_birth')
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    $creds = Student::passwordCredentialsFromDateOfBirth((string) $row->date_of_birth);
                    if (! $creds) {
                        continue;
                    }
                    DB::table('students')->where('id', $row->id)->update([
                        'password' => $creds['password'],
                        'password_plain_encrypted' => $creds['password_plain_encrypted'],
                        'updated_at' => now(),
                    ]);
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
