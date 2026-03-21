<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Institute;

class InstituteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Institute::updateOrCreate(
            ['domain' => 'mjpitm.in'],
            [
                'name' => 'Mahatma Jyotiba Phule Institute of Technology & Management',
                'description' => 'Technical & Management courses (BCA, BBA, etc.)',
                'status' => 'active',
            ]
        );

        Institute::updateOrCreate(
            ['domain' => 'mjpips.in'],
            [
                'name' => 'Mahatma Jyotiba Phule Institute of Paramedical Science',
                'description' => 'Paramedical & health science courses (DMLT, B.Sc Nursing, etc.)',
                'status' => 'active',
            ]
        );
    }
}
