<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Course Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.courses.edit', $course->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Course
                </a>
                <a href="{{ route('admin.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back to Courses
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Basic Course Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">Course Information</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Institute</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $course->institute->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Course Code</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $course->code }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Course Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $course->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $course->formatted_duration }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($course->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($course->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $course->description }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Fee Structure -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-yellow-50 border-b border-yellow-200">
                    <h3 class="text-lg font-semibold text-yellow-900">Fee Structure</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Registration Fee</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">₹{{ number_format($course->registration_fee ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Entrance Fee</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">₹{{ number_format($course->entrance_fee ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Enrollment Fee</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">₹{{ number_format($course->enrollment_fee ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tuition Fee</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">₹{{ number_format($course->tuition_fee ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Caution Money</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">₹{{ number_format($course->caution_money ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Hostel Fee</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">₹{{ number_format($course->hostel_fee_amount ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Late Fee</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">₹{{ number_format($course->late_fee ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Fees</dt>
                            <dd class="mt-1 text-lg text-gray-900 font-bold">
                                ₹{{ number_format(($course->registration_fee ?? 0) + ($course->entrance_fee ?? 0) + ($course->enrollment_fee ?? 0) + ($course->tuition_fee ?? 0) + ($course->caution_money ?? 0) + ($course->hostel_fee_amount ?? 0) + ($course->late_fee ?? 0), 2) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-green-50 border-b border-green-200">
                    <h3 class="text-lg font-semibold text-green-900">Statistics</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Students</dt>
                            <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $course->students->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Subjects</dt>
                            <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $course->subjects->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $course->created_at->format('d M Y') }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semester Subjects Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-indigo-50 border-b border-indigo-200">
                    <h3 class="text-lg font-semibold text-indigo-900">Manage Semester Subjects</h3>
                    <p class="text-sm text-indigo-700 mt-1">Add or edit subjects for each semester of this course</p>
                </div>
                <div class="p-6">
                    @php
                        $semesters = $course->subjects()->distinct()->pluck('semester')->sort()->values();
                        $nextSemester = $semesters->count() > 0 ? $semesters->max() + 1 : 1;
                    @endphp
                    
                    @if($semesters->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-4">
                            @foreach($semesters as $sem)
                                @php
                                    $subjectCount = $course->subjects()->where('semester', $sem)->count();
                                @endphp
                                <a href="{{ route('admin.courses.semester.subjects', [$course->id, $sem]) }}" 
                                   class="block p-4 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg text-center transition">
                                    <div class="text-lg font-bold text-indigo-900">Semester {{ $sem }}</div>
                                    <div class="text-sm text-indigo-600 mt-1">{{ $subjectCount }} {{ $subjectCount == 1 ? 'Subject' : 'Subjects' }}</div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg text-center">
                            <p class="text-gray-600 mb-3">No semesters added yet. Start by adding Semester 1.</p>
                        </div>
                    @endif
                    
                    <!-- Add New Semester Button -->
                    <div class="mt-4">
                        <a href="{{ route('admin.courses.semester.subjects', [$course->id, $nextSemester]) }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Semester {{ $nextSemester }}
                        </a>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-4">Click on any semester to add or edit subjects, or add a new semester</p>
                </div>
            </div>

            <!-- Students List (if any) -->
            @if($course->students->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-purple-50 border-b border-purple-200">
                    <h3 class="text-lg font-semibold text-purple-900">Enrolled Students ({{ $course->students->count() }})</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($course->students->take(10) as $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->roll_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->current_semester }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($course->students->count() > 10)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.students.index', ['course_id' => $course->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                View all {{ $course->students->count() }} students →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

