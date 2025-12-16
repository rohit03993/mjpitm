<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            @if (session('password_shown'))
                @php
                    $passwordData = session('password_shown');
                @endphp
                <div class="mb-6 bg-blue-50 border-2 border-blue-300 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-medium text-blue-900 mb-2">
                                Password for {{ $passwordData['user_name'] }} (ID: {{ $passwordData['user_id'] }})
                            </h3>
                            <div class="mt-3 bg-white rounded-md p-4 border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password:</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="text" id="password-display" value="{{ $passwordData['password'] }}" readonly 
                                                   class="block w-full rounded-md border-gray-300 bg-gray-50 font-mono text-lg font-bold text-gray-900">
                                            <button onclick="copyPassword()" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                                Copy
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 text-xs text-gray-600">
                                    <strong>Important:</strong> This password is shown only once. Make sure to copy it now. It cannot be retrieved later.
                                </p>
                            </div>
                        </div>
                        <button onclick="closePasswordDisplay()" class="ml-4 text-blue-400 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Tabs -->
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <a href="{{ route('superadmin.manage-users.index', ['tab' => 'staff']) }}" 
                           class="{{ $tab === 'staff' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Staff
                            <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">
                                {{ \App\Models\User::where('role', 'staff')->count() }}
                            </span>
                        </a>
                        <a href="{{ route('superadmin.manage-users.index', ['tab' => 'institute_admin']) }}" 
                           class="{{ $tab === 'institute_admin' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Institute Admin (Guest)
                            <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">
                                {{ \App\Models\User::where('role', 'institute_admin')->count() }}
                            </span>
                        </a>
                        <a href="{{ route('superadmin.manage-users.index', ['tab' => 'students']) }}" 
                           class="{{ $tab === 'students' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Students
                            <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">
                                {{ \App\Models\Student::count() }}
                            </span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                @if($tab === 'staff')
                                    Staff Users
                                @elseif($tab === 'institute_admin')
                                    Institute Admin (Guest) Users
                                @else
                                    Students
                                @endif
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Manage user accounts and passwords</p>
                        </div>
                        <div>
                            @if($tab === 'staff')
                                <a href="{{ route('superadmin.users.create', ['role' => 'staff']) }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    + Add Staff
                                </a>
                            @elseif($tab === 'institute_admin')
                                <a href="{{ route('superadmin.users.create', ['role' => 'institute_admin']) }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    + Add Institute Admin
                                </a>
                            @elseif($tab === 'students')
                                <a href="{{ route('admin.students.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    + Add Student
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Desktop Table View (hidden on mobile) -->
                    <div class="hidden lg:block overflow-hidden">
                        <table class="w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    @if($tab === 'students')
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration No.</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No.</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    @else
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institute</th>
                                    @endif
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Password</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ $user->id }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $user->name }}
                                        </td>
                                        @if($tab === 'students')
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                {{ $user->registration_number ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                {{ $user->roll_number ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                <div class="max-w-xs truncate" title="{{ $user->course->name ?? 'N/A' }}">
                                                    {{ $user->course->name ?? 'N/A' }}
                                                </div>
                                            </td>
                                        @else
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                <div class="max-w-xs truncate" title="{{ $user->email }}">
                                                    {{ $user->email }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                {{ $user->institute->name ?? 'All Institutes' }}
                                            </td>
                                        @endif
                                        <td class="px-4 py-3">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ ($tab === 'students' ? $user->status : $user->status) === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($tab === 'students' ? $user->status : $user->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            <span class="text-xs text-gray-400">••••••••</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ $tab === 'students' ? route('superadmin.manage-users.view-student-password', $user->id) : route('superadmin.manage-users.view-user-password', $user->id) }}" 
                                                   class="text-blue-600 hover:text-blue-900 text-xs whitespace-nowrap">
                                                    View
                                                </a>
                                                <span class="text-gray-300">|</span>
                                                <form method="POST" action="{{ $tab === 'students' ? route('superadmin.manage-users.generate-student-password', $user->id) : route('superadmin.manage-users.generate-user-password', $user->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900 text-xs whitespace-nowrap"
                                                            onclick="return confirm('Generate new password?')">
                                                        Generate
                                                    </button>
                                                </form>
                                                <span class="text-gray-300">|</span>
                                                <button onclick="openPasswordModal({{ $user->id }}, {{ json_encode($user->name) }}, '{{ $tab }}')" 
                                                        class="text-indigo-600 hover:text-indigo-900 text-xs whitespace-nowrap">
                                                    Change Password
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $tab === 'students' ? '8' : '7' }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile/Tablet Card View (visible on mobile/tablet, hidden on desktop) -->
                    <div class="block lg:hidden space-y-4">
                        @forelse($users as $user)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h4 class="text-base font-semibold text-gray-900">{{ $user->name }}</h4>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                                {{ ($tab === 'students' ? $user->status : $user->status) === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($tab === 'students' ? $user->status : $user->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-4">
                                    @if($tab === 'students')
                                        <div class="flex items-start">
                                            <span class="text-xs font-medium text-gray-500 w-32 flex-shrink-0">Registration No.:</span>
                                            <span class="text-sm text-gray-900">{{ $user->registration_number ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-xs font-medium text-gray-500 w-32 flex-shrink-0">Roll No.:</span>
                                            <span class="text-sm text-gray-900">{{ $user->roll_number ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-xs font-medium text-gray-500 w-32 flex-shrink-0">Course:</span>
                                            <span class="text-sm text-gray-900">{{ $user->course->name ?? 'N/A' }}</span>
                                        </div>
                                    @else
                                        <div class="flex items-start">
                                            <span class="text-xs font-medium text-gray-500 w-24 flex-shrink-0">Email:</span>
                                            <span class="text-sm text-gray-900 break-all">{{ $user->email }}</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-xs font-medium text-gray-500 w-24 flex-shrink-0">Institute:</span>
                                            <span class="text-sm text-gray-900">{{ $user->institute->name ?? 'All Institutes' }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-start">
                                        <span class="text-xs font-medium text-gray-500 w-32 flex-shrink-0">Password:</span>
                                        <span class="text-xs text-gray-400">••••••••</span>
                                    </div>
                                </div>
                                
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ $tab === 'students' ? route('superadmin.manage-users.view-student-password', $user->id) : route('superadmin.manage-users.view-user-password', $user->id) }}" 
                                           class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">
                                            View Password
                                        </a>
                                        <form method="POST" action="{{ $tab === 'students' ? route('superadmin.manage-users.generate-student-password', $user->id) : route('superadmin.manage-users.generate-user-password', $user->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100"
                                                    onclick="return confirm('Generate new password?')">
                                                Generate New
                                            </button>
                                        </form>
                                        <button onclick="openPasswordModal({{ $user->id }}, {{ json_encode($user->name) }}, '{{ $tab }}')" 
                                                class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                                            Change Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-sm text-gray-500">
                                No users found.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Update Modal -->
    <div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
                    <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    Update password for: <strong id="modalUserName"></strong> (ID: <span id="modalUserId"></span>)
                </p>
                <form id="passwordForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                        <input type="password" id="password" name="password" required minlength="8" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closePasswordModal()" 
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openPasswordModal(userId, userName, userType) {
            document.getElementById('modalUserId').textContent = userId;
            document.getElementById('modalUserName').textContent = userName;
            
            let formAction;
            if (userType === 'students') {
                formAction = '/superadmin/manage-users/student/' + userId + '/update-password';
            } else {
                formAction = '/superadmin/manage-users/user/' + userId + '/update-password';
            }
            
            document.getElementById('passwordForm').action = formAction;
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
            document.getElementById('passwordForm').reset();
        }

        function closePasswordDisplay() {
            const display = document.querySelector('.bg-blue-50');
            if (display) {
                display.style.display = 'none';
            }
        }

        function copyPassword() {
            const passwordInput = document.getElementById('password-display');
            passwordInput.select();
            passwordInput.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            
            // Show feedback
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            button.classList.add('bg-green-600');
            button.classList.remove('bg-blue-600');
            
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('bg-green-600');
                button.classList.add('bg-blue-600');
            }, 2000);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('passwordModal');
            if (event.target === modal) {
                closePasswordModal();
            }
        }
    </script>
</x-app-layout>

