<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Institute;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get institutes
        $techInstitute = Institute::where('domain', 'mjpitm.in')->first();
        $paramedicalInstitute = Institute::where('domain', 'mjpips.in')->first();

        if ($techInstitute) {
            // Tech Institute Courses
            Course::updateOrCreate(
                ['code' => 'BCA'],
                [
                    'institute_id' => $techInstitute->id,
                    'name' => 'Bachelor of Computer Applications',
                    'duration_years' => 3,
                    'description' => 'Bachelor of Computer Applications - A three-year undergraduate program in computer applications.',
                    'status' => 'active',
                    'registration_fee' => 1000.00,
                    'entrance_fee' => 500.00,
                    'enrollment_fee' => 2000.00,
                    'tuition_fee' => 45000.00,
                    'caution_money' => 5000.00,
                    'hostel_fee_amount' => 20000.00,
                    'late_fee' => 500.00,
                ]
            );

            Course::updateOrCreate(
                ['code' => 'BBA'],
                [
                    'institute_id' => $techInstitute->id,
                    'name' => 'Bachelor of Business Administration',
                    'duration_years' => 3,
                    'description' => 'Bachelor of Business Administration - A three-year undergraduate program in business administration.',
                    'status' => 'active',
                    'registration_fee' => 1000.00,
                    'entrance_fee' => 500.00,
                    'enrollment_fee' => 2000.00,
                    'tuition_fee' => 40000.00,
                    'caution_money' => 5000.00,
                    'hostel_fee_amount' => 20000.00,
                    'late_fee' => 500.00,
                ]
            );

            Course::updateOrCreate(
                ['code' => 'MCA'],
                [
                    'institute_id' => $techInstitute->id,
                    'name' => 'Master of Computer Applications',
                    'duration_years' => 2,
                    'description' => 'Master of Computer Applications - A two-year postgraduate program in computer applications.',
                    'status' => 'active',
                    'registration_fee' => 1500.00,
                    'entrance_fee' => 750.00,
                    'enrollment_fee' => 2500.00,
                    'tuition_fee' => 55000.00,
                    'caution_money' => 5000.00,
                    'hostel_fee_amount' => 20000.00,
                    'late_fee' => 750.00,
                ]
            );
        }

        if ($paramedicalInstitute) {
            // Paramedical Institute Courses
            Course::updateOrCreate(
                ['code' => 'DMLT'],
                [
                    'institute_id' => $paramedicalInstitute->id,
                    'name' => 'Diploma in Medical Laboratory Technology',
                    'duration_years' => 2,
                    'description' => 'Diploma in Medical Laboratory Technology - A two-year diploma program in medical laboratory technology.',
                    'status' => 'active',
                    'registration_fee' => 800.00,
                    'entrance_fee' => 400.00,
                    'enrollment_fee' => 1500.00,
                    'tuition_fee' => 35000.00,
                    'caution_money' => 3000.00,
                    'hostel_fee_amount' => 18000.00,
                    'late_fee' => 400.00,
                ]
            );

            Course::updateOrCreate(
                ['code' => 'B.Sc Nursing'],
                [
                    'institute_id' => $paramedicalInstitute->id,
                    'name' => 'Bachelor of Science in Nursing',
                    'duration_years' => 4,
                    'description' => 'Bachelor of Science in Nursing - A four-year undergraduate program in nursing.',
                    'status' => 'active',
                    'registration_fee' => 1200.00,
                    'entrance_fee' => 600.00,
                    'enrollment_fee' => 2500.00,
                    'tuition_fee' => 60000.00,
                    'caution_money' => 6000.00,
                    'hostel_fee_amount' => 25000.00,
                    'late_fee' => 600.00,
                ]
            );

            Course::updateOrCreate(
                ['code' => 'BPT'],
                [
                    'institute_id' => $paramedicalInstitute->id,
                    'name' => 'Bachelor of Physiotherapy',
                    'duration_years' => 4,
                    'description' => 'Bachelor of Physiotherapy - A four-year undergraduate program in physiotherapy.',
                    'status' => 'active',
                    'registration_fee' => 1200.00,
                    'entrance_fee' => 600.00,
                    'enrollment_fee' => 2500.00,
                    'tuition_fee' => 58000.00,
                    'caution_money' => 6000.00,
                    'hostel_fee_amount' => 25000.00,
                    'late_fee' => 600.00,
                ]
            );
        }
    }
}

