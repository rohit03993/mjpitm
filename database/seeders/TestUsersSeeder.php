<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Institute;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates test users for Staff and Students for testing purposes.
     */
    public function run(): void
    {
        // Get the first institute (Tech Institute)
        $techInstitute = Institute::where('domain', 'mjpitm.in')->first();
        $paraInstitute = Institute::where('domain', 'mjpips.in')->first();
        
        // Create Staff/Institute Admin for Tech Institute
        $staff1 = User::firstOrCreate(
            ['email' => 'staff@mjpitm.in'],
            [
                'name' => 'Tech Institute Staff',
                'password' => Hash::make('password123'),
                'role' => 'institute_admin',
                'institute_id' => $techInstitute?->id,
                'status' => 'active',
            ]
        );
        
        // Create Staff/Institute Admin for Paramedical Institute
        $staff2 = User::firstOrCreate(
            ['email' => 'staff@mjpips.in'],
            [
                'name' => 'Paramedical Institute Staff',
                'password' => Hash::make('password123'),
                'role' => 'institute_admin',
                'institute_id' => $paraInstitute?->id,
                'status' => 'active',
            ]
        );

        // Get a course from each institute
        $techCourse = Course::where('institute_id', $techInstitute?->id)->first();
        $paraCourse = Course::where('institute_id', $paraInstitute?->id)->first();

        // Create Test Student for Tech Institute
        $student1 = Student::firstOrCreate(
            ['email' => 'student@mjpitm.in'],
            [
                'institute_id' => $techInstitute?->id,
                'course_id' => $techCourse?->id,
                'registration_number' => 'REG-TEST-001',
                'roll_number' => 'TECH-2025-001',
                'name' => 'Test Student (Tech)',
                'father_name' => 'Test Father',
                'mother_name' => 'Test Mother',
                'date_of_birth' => '2000-01-15',
                'gender' => 'male',
                'phone' => '9876543210',
                'address' => 'Test Address, City',
                'country' => 'India',
                'nationality' => 'Indian',
                'state' => 'Uttar Pradesh',
                'district' => 'Lucknow',
                'pin_code' => '226001',
                'password' => Hash::make('student123'),
                'admission_year' => '2025',
                'session' => '2025-2026',
                'mode_of_study' => 'regular',
                'current_semester' => 1,
                'status' => 'active',
                'declaration_accepted' => true,
                'created_by' => $staff1?->id ?? 1,
            ]
        );

        // Create Test Student for Paramedical Institute
        $student2 = Student::firstOrCreate(
            ['email' => 'student@mjpips.in'],
            [
                'institute_id' => $paraInstitute?->id,
                'course_id' => $paraCourse?->id,
                'registration_number' => 'REG-TEST-002',
                'roll_number' => 'PARA-2025-001',
                'name' => 'Test Student (Paramedical)',
                'father_name' => 'Test Father',
                'mother_name' => 'Test Mother',
                'date_of_birth' => '2001-05-20',
                'gender' => 'female',
                'phone' => '9876543211',
                'address' => 'Test Address 2, City',
                'country' => 'India',
                'nationality' => 'Indian',
                'state' => 'Uttar Pradesh',
                'district' => 'Lucknow',
                'pin_code' => '226002',
                'password' => Hash::make('student123'),
                'admission_year' => '2025',
                'session' => '2025-2026',
                'mode_of_study' => 'regular',
                'current_semester' => 1,
                'status' => 'active',
                'declaration_accepted' => true,
                'created_by' => $staff2?->id ?? 1,
            ]
        );

        $this->command->info('Test users created successfully!');
        $this->command->info('');
        $this->command->info('=== LOGIN CREDENTIALS ===');
        $this->command->info('');
        $this->command->info('SUPER ADMIN:');
        $this->command->info('  Email: superadmin@gurukul.edu');
        $this->command->info('  Password: password');
        $this->command->info('  Login URL: /superadmin/login');
        $this->command->info('');
        $this->command->info('STAFF (Tech Institute):');
        $this->command->info('  Email: staff@mjpitm.in');
        $this->command->info('  Password: password123');
        $this->command->info('  Login URL: /staff/login');
        $this->command->info('');
        $this->command->info('STAFF (Paramedical Institute):');
        $this->command->info('  Email: staff@mjpips.in');
        $this->command->info('  Password: password123');
        $this->command->info('  Login URL: /staff/login');
        $this->command->info('');
        $this->command->info('STUDENT (Tech Institute):');
        $this->command->info('  Registration: REG-TEST-001 OR Roll: TECH-2025-001');
        $this->command->info('  Password: student123');
        $this->command->info('  Login URL: /student/login');
        $this->command->info('');
        $this->command->info('STUDENT (Paramedical Institute):');
        $this->command->info('  Registration: REG-TEST-002 OR Roll: PARA-2025-001');
        $this->command->info('  Password: student123');
        $this->command->info('  Login URL: /student/login');
    }
}

