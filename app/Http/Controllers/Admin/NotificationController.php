<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the current admin
     */
    public function getUnread(Request $request)
    {
        $user = Auth::user();
        
        $query = RegistrationNotification::with(['student', 'institute'])
            ->whereNull('read_at');

        // Super Admin sees all, normal admin sees only their institute
        if (!$user->isSuperAdmin()) {
            $instituteId = session('current_institute_id');
            if ($instituteId) {
                $query->where('institute_id', $instituteId);
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'student_id' => $notification->student_id,
                    'student_name' => $notification->student->name ?? 'Unknown',
                    'registration_type' => $notification->registration_type,
                    'institute_name' => $notification->institute->name ?? 'Unknown',
                    'created_at' => $notification->created_at->diffForHumans(),
                    'url' => route('admin.students.show', $notification->student_id),
                ];
            });

        $count = $query->count();

        return response()->json([
            'notifications' => $notifications,
            'count' => $count,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = RegistrationNotification::findOrFail($id);
        
        // Check authorization
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            $instituteId = session('current_institute_id');
            if ($notification->institute_id != $instituteId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $notification->markAsRead($user->id);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        $query = RegistrationNotification::whereNull('read_at');

        if (!$user->isSuperAdmin()) {
            $instituteId = session('current_institute_id');
            if ($instituteId) {
                $query->where('institute_id', $instituteId);
            }
        }

        $query->update([
            'read_by' => $user->id,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}

