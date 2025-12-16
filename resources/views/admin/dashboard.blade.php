<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Institute-wise Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Tech Institute Overview -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-blue-100">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $techStats['institute']->name ?? 'Tech Institute' }}
                                </h3>
                                <p class="text-sm text-gray-500">Technology & Management Programs</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                Tech
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Students</div>
                                <div class="mt-1 text-2xl font-bold text-blue-900">
                                    {{ $techStats['students_total'] }}
                                </div>
                                <div class="text-xs text-blue-700 mt-1">
                                    {{ $techStats['students_active'] }} active
                                </div>
                            </div>
                            <div class="bg-indigo-50 rounded-lg p-4">
                                <div class="text-xs font-semibold text-indigo-700 uppercase tracking-wide">Courses</div>
                                <div class="mt-1 text-2xl font-bold text-indigo-900">
                                    {{ $techStats['courses_total'] }}
                                </div>
                                <div class="text-xs text-indigo-700 mt-1">
                                    {{ $techStats['courses_active'] }} active
                                </div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4 col-span-2">
                                <div class="text-xs font-semibold text-yellow-700 uppercase tracking-wide">Total Fees (All Students)</div>
                                <div class="mt-1 text-2xl font-bold text-yellow-900">
                                    ₹{{ number_format($techStats['fees_total'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paramedical Institute Overview -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-100">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $paramedicalStats['institute']->name ?? 'Paramedical Institute' }}
                                </h3>
                                <p class="text-sm text-gray-500">Paramedical & Healthcare Programs</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                Paramedical
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-xs font-semibold text-green-700 uppercase tracking-wide">Students</div>
                                <div class="mt-1 text-2xl font-bold text-green-900">
                                    {{ $paramedicalStats['students_total'] }}
                                </div>
                                <div class="text-xs text-green-700 mt-1">
                                    {{ $paramedicalStats['students_active'] }} active
                                </div>
                            </div>
                            <div class="bg-emerald-50 rounded-lg p-4">
                                <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Courses</div>
                                <div class="mt-1 text-2xl font-bold text-emerald-900">
                                    {{ $paramedicalStats['courses_total'] }}
                                </div>
                                <div class="text-xs text-emerald-700 mt-1">
                                    {{ $paramedicalStats['courses_active'] }} active
                                </div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4 col-span-2">
                                <div class="text-xs font-semibold text-yellow-700 uppercase tracking-wide">Total Fees (All Students)</div>
                                <div class="mt-1 text-2xl font-bold text-yellow-900">
                                    ₹{{ number_format($paramedicalStats['fees_total'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isInstituteAdmin())
            <!-- Institute Admin Earnings/Owed Amount -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-2 border-purple-200">
                <div class="p-6 bg-gradient-to-r from-purple-50 to-purple-100 border-b border-purple-200">
                    <h3 class="text-lg font-semibold text-purple-900">Your Registration Summary</h3>
                    <p class="text-sm text-purple-700 mt-1">Amount you need to pay to the institute</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-purple-50 rounded-lg p-6 border-2 border-purple-200">
                            <div class="text-xs font-semibold text-purple-700 uppercase tracking-wide mb-2">Total Registrations</div>
                            <div class="text-4xl font-bold text-purple-900">
                                {{ $totalRegistrations }}
                            </div>
                            <div class="text-sm text-purple-600 mt-2">Students registered by you</div>
                        </div>
                        <div class="bg-purple-100 rounded-lg p-6 border-2 border-purple-300">
                            <div class="text-xs font-semibold text-purple-800 uppercase tracking-wide mb-2">Total Amount Owed</div>
                            <div class="text-4xl font-bold text-purple-900">
                                ₹{{ number_format($totalAmountOwed, 2) }}
                            </div>
                            <div class="text-sm text-purple-700 mt-2">Based on course durations</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-6 border-2 border-purple-200">
                            <div class="text-xs font-semibold text-purple-700 uppercase tracking-wide mb-2">Fee Structure</div>
                            <div class="text-sm text-purple-800 space-y-1">
                                <div>3-6 months: ₹2,500</div>
                                <div>1 year: ₹3,500</div>
                                <div>2 years: ₹4,500</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <p class="text-sm text-purple-800">
                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <strong>Note:</strong> This is the total amount you need to pay to the institute for all students you have registered. 
                            The amount is calculated based on each course's duration at the time of registration.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Everyone: student registration & list (staff sees only their own students) -->
                        <a href="{{ route('admin.students.index') }}" class="p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition relative">
                            <h4 class="font-medium text-blue-900">All Students</h4>
                            <p class="text-sm text-blue-700">View and manage all students (with filters for type, status, etc.)</p>
                            @php
                                $pendingCount = \App\Models\Student::whereNull('created_by')
                                    ->where('status', 'pending')
                                    ->when(!auth()->user()->isSuperAdmin(), function($q) {
                                        $instituteId = session('current_institute_id');
                                        if ($instituteId) {
                                            $q->where('institute_id', $instituteId);
                                        }
                                    })
                                    ->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="absolute top-2 right-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $pendingCount }} Pending
                                </span>
                            @endif
                        </a>

                        @if(auth()->user() && auth()->user()->isSuperAdmin())
                            <!-- Only Admin gets course / results management -->
                            <a href="{{ route('admin.courses.index') }}" class="p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                <h4 class="font-medium text-green-900">Manage Courses</h4>
                                <p class="text-sm text-green-700">Add, edit, or view courses</p>
                            </a>
                            <a href="#" class="p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                                <h4 class="font-medium text-purple-900">Manage Results</h4>
                                <p class="text-sm text-purple-700">Upload and verify results</p>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Students -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Students (All Institutes)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institute</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentStudents as $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->roll_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($student->institute)
                                                @php
                                                    $isParamedical = \Illuminate\Support\Str::contains(
                                                        \Illuminate\Support\Str::lower($student->institute->name),
                                                        'paramedical'
                                                    );
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $isParamedical ? 'bg-green-50 text-green-800' : 'bg-blue-50 text-blue-800' }}">
                                                    {{ $student->institute->name }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->course->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->current_semester }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $student->creator->name ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($student->status === 'active')
                                                    bg-green-100 text-green-800
                                                @elseif($student->status === 'pending')
                                                    bg-yellow-100 text-yellow-800
                                                @else
                                                    bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No students found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

