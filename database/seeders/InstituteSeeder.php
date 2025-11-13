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
        Institute::create([
            'name' => 'Mahatma Jyotiba Phule Institute of Technology & Management',
            'domain' => 'mjpitm.in',
            'description' => 'Technical & Management courses (BCA, BBA, etc.)',
            'status' => 'active',
        ]);

        Institute::create([
            'name' => 'Mahatma Jyotiba Phule Institute of Paramedical Science',
            'domain' => 'mjpips.in',
            'description' => 'Paramedical & health science courses (DMLT, B.Sc Nursing, etc.)',
            'status' => 'active',
        ]);
    }
}
