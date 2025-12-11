<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManageUsersController extends Controller
{
    /**
     * Display the manage users page with tabs.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'staff'); // Default to staff tab
        
        // Get users based on selected tab
        switch ($tab) {
            case 'staff':
                $users = User::where('role', 'staff')
                    ->with('institute')
                    ->latest()
                    ->get();
                break;
                
            case 'institute_admin':
                $users = User::where('role', 'institute_admin')
                    ->with('institute')
                    ->latest()
                    ->get();
                break;
                
            case 'students':
                $users = Student::with(['institute', 'course'])
                    ->latest()
                    ->get();
                break;
                
            default:
                $users = collect();
        }
        
        return view('superadmin.manage-users.index', compact('users', 'tab'));
    }
    
    /**
     * View password for a User (Staff or Institute Admin).
     */
    public function viewUserPassword(User $user)
    {
        $plainPassword = $user->plain_password;
        
        if (!$plainPassword) {
            return redirect()->back()
                ->with('error', 'Password not available for ' . $user->name . ' (ID: ' . $user->id . '). Password may not have been stored in encrypted format.');
        }
        
        return redirect()->back()
            ->with('password_shown', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'password' => $plainPassword,
                'type' => 'user'
            ]);
    }
    
    /**
     * View password for a Student.
     */
    public function viewStudentPassword(Student $student)
    {
        $plainPassword = $student->plain_password;
        
        if (!$plainPassword) {
            return redirect()->back()
                ->with('error', 'Password not available for ' . $student->name . ' (ID: ' . $student->id . '). Password may not have been stored in encrypted format.');
        }
        
        return redirect()->back()
            ->with('password_shown', [
                'user_id' => $student->id,
                'user_name' => $student->name,
                'password' => $plainPassword,
                'type' => 'student'
            ]);
    }
    
    /**
     * Generate and show a new password for a User (Staff or Institute Admin).
     */
    public function generateUserPassword(User $user)
    {
        // Generate a random 12-character password
        $newPassword = $this->generateRandomPassword();
        
        // Hash and save the password (for authentication)
        $user->password = Hash::make($newPassword);
        // Encrypt and save the plain password (for Super Admin viewing)
        $user->setPlainPassword($newPassword);
        $user->save();
        
        // Return the plain password so admin can see it
        return redirect()->back()
            ->with('password_shown', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'password' => $newPassword,
                'type' => 'user'
            ])
            ->with('success', 'New password generated for ' . $user->name . ' (ID: ' . $user->id . ')');
    }
    
    /**
     * Generate and show a new password for a Student.
     */
    public function generateStudentPassword(Student $student)
    {
        // Generate a random 12-character password
        $newPassword = $this->generateRandomPassword();
        
        // Hash and save the password (for authentication)
        $student->password = Hash::make($newPassword);
        // Encrypt and save the plain password (for Super Admin viewing)
        $student->setPlainPassword($newPassword);
        $student->save();
        
        // Return the plain password so admin can see it
        return redirect()->back()
            ->with('password_shown', [
                'user_id' => $student->id,
                'user_name' => $student->name,
                'password' => $newPassword,
                'type' => 'student'
            ])
            ->with('success', 'New password generated for ' . $student->name . ' (ID: ' . $student->id . ')');
    }
    
    /**
     * Update password for a User (Staff or Institute Admin).
     */
    public function updateUserPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        // Hash and save the password (for authentication)
        $user->password = Hash::make($validated['password']);
        // Encrypt and save the plain password (for Super Admin viewing)
        $user->setPlainPassword($validated['password']);
        $user->save();
        
        return redirect()->back()
            ->with('password_shown', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'password' => $validated['password'],
                'type' => 'user'
            ])
            ->with('success', 'Password updated successfully for ' . $user->name . ' (ID: ' . $user->id . ')');
    }
    
    /**
     * Update password for a Student.
     */
    public function updateStudentPassword(Request $request, Student $student)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        // Hash and save the password (for authentication)
        $student->password = Hash::make($validated['password']);
        // Encrypt and save the plain password (for Super Admin viewing)
        $student->setPlainPassword($validated['password']);
        $student->save();
        
        return redirect()->back()
            ->with('password_shown', [
                'user_id' => $student->id,
                'user_name' => $student->name,
                'password' => $validated['password'],
                'type' => 'student'
            ])
            ->with('success', 'Password updated successfully for ' . $student->name . ' (ID: ' . $student->id . ')');
    }
    
    /**
     * Generate a random password.
     */
    private function generateRandomPassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        $max = strlen($characters) - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $max)];
        }
        
        return $password;
    }
}
