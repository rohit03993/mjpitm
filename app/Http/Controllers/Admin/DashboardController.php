<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Course;
use App\Models\Institute;

class DashboardController extends Controller
{
    /**
     * Display the institute admin dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Load both institutes (Tech & Paramedical) by domain so IDs are consistent
        $techInstitute = Institute::where('domain', 'mjpitm.in')->first();
        $paramedicalInstitute = Institute::where('domain', 'mjpips.in')->first();

        // Helper closure to compute basic stats per institute
        // If $forUserId is provided, stats are limited to students created by that admin
        $buildStats = function (?Institute $institute, ?int $forUserId = null) {
            if (!$institute) {
                return [
                    'institute' => null,
                    'students_total' => 0,
                    'students_active' => 0,
                    'courses_total' => 0,
                    'courses_active' => 0,
                    'fees_total' => 0,
                ];
            }

            $instituteId = $institute->id;

            $studentQuery = Student::where('institute_id', $instituteId);
            if ($forUserId) {
                $studentQuery->where('created_by', $forUserId);
            }

            return [
                'institute' => $institute,
                'students_total' => (clone $studentQuery)->count(),
                'students_active' => (clone $studentQuery)
                    ->where('status', 'active')
                    ->count(),
                'courses_total' => Course::where('institute_id', $instituteId)->count(),
                'courses_active' => Course::where('institute_id', $instituteId)
                    ->where('status', 'active')
                    ->count(),
                // Sum of total_deposit from student records (already includes all fee components)
                'fees_total' => (clone $studentQuery)->sum('total_deposit'),
            ];
        };

        if ($user->isSuperAdmin()) {
            // Super Admin sees overall stats across all admins
            $techStats = $buildStats($techInstitute);
            $paramedicalStats = $buildStats($paramedicalInstitute);

            // Recent students across all institutes
            $recentStudents = Student::with(['institute', 'course', 'creator'])
                ->latest()
                ->take(5)
                ->get();
        } else {
            // Normal admins see only students they have created
            $techStats = $buildStats($techInstitute, $user->id);
            $paramedicalStats = $buildStats($paramedicalInstitute, $user->id);

            // Recent students created by this admin
            $recentStudents = Student::with(['institute', 'course', 'creator'])
                ->where('created_by', $user->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('admin.dashboard', compact(
            'techStats',
            'paramedicalStats',
            'recentStudents'
        ));
    }
}
