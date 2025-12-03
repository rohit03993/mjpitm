<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Result;
use App\Models\Subject;
use App\Models\Qualification;
use App\Models\CourseCategory;

class SystemResetController extends Controller
{
    /**
     * Show the reset confirmation page.
     */
    public function index()
    {
        // Get counts for display
        $counts = [
            'students' => Student::count(),
            'guests' => User::where('role', '!=', 'super_admin')->count(),
            'courses' => Course::count(),
            'categories' => CourseCategory::count(),
            'subjects' => Subject::count(),
            'fees' => Fee::count(),
            'results' => Result::count(),
        ];

        return view('superadmin.system-reset', compact('counts'));
    }

    /**
     * Reset all system data.
     */
    public function reset(Request $request)
    {
        // Validate confirmation
        $request->validate([
            'confirmation' => ['required', 'string', function ($attribute, $value, $fail) {
                if ($value !== 'RESET ALL DATA') {
                    $fail('Please type "RESET ALL DATA" exactly to confirm.');
                }
            }],
        ]);

        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Delete all data
            Fee::truncate();
            Result::truncate();
            Qualification::truncate();
            Student::truncate();
            Subject::truncate();
            Course::truncate();
            CourseCategory::truncate();
            User::where('role', '!=', 'super_admin')->delete();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('superadmin.system-reset')
                ->with('success', 'All system data has been reset successfully. The system is now fresh!');

        } catch (\Exception $e) {
            // Re-enable foreign key checks even on error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->route('superadmin.system-reset')
                ->with('error', 'Failed to reset data: ' . $e->getMessage());
        }
    }

    /**
     * Reset only students and related data (fees, results, qualifications).
     */
    public function resetStudents(Request $request)
    {
        $request->validate([
            'confirmation' => ['required', 'string', function ($attribute, $value, $fail) {
                if ($value !== 'RESET STUDENTS') {
                    $fail('Please type "RESET STUDENTS" exactly to confirm.');
                }
            }],
        ]);

        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            Fee::truncate();
            Result::truncate();
            Qualification::truncate();
            Student::truncate();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('superadmin.system-reset')
                ->with('success', 'All students and related data have been reset successfully.');

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->route('superadmin.system-reset')
                ->with('error', 'Failed to reset students: ' . $e->getMessage());
        }
    }

    /**
     * Reset only guest users.
     */
    public function resetGuests(Request $request)
    {
        $request->validate([
            'confirmation' => ['required', 'string', function ($attribute, $value, $fail) {
                if ($value !== 'RESET GUESTS') {
                    $fail('Please type "RESET GUESTS" exactly to confirm.');
                }
            }],
        ]);

        try {
            // First, set created_by to null for all students created by guests
            Student::whereNotNull('created_by')
                ->whereHas('creator', function($q) {
                    $q->where('role', '!=', 'super_admin');
                })
                ->update(['created_by' => null]);

            // Delete all non-super admin users
            User::where('role', '!=', 'super_admin')->delete();

            return redirect()->route('superadmin.system-reset')
                ->with('success', 'All guest accounts have been removed successfully.');

        } catch (\Exception $e) {
            return redirect()->route('superadmin.system-reset')
                ->with('error', 'Failed to reset guests: ' . $e->getMessage());
        }
    }
}

