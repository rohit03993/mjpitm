<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('All Students') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">View and manage all students (Active, Pending, and more)</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.students.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    + Register New Student
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Status Summary Cards -->
                    @if(isset($statusCounts))
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-600">Total Students</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $statusCounts['all'] }}</p>
                                </div>
                                <svg class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600">Active Students</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $statusCounts['active'] }}</p>
                                </div>
                                <svg class="h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-yellow-600">Pending Students</p>
                                    <p class="text-2xl font-bold text-yellow-900">{{ $statusCounts['pending'] }}</p>
                                </div>
                                <svg class="h-8 w-8 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Search and Filters -->
                    <form method="GET" action="{{ route('admin.students.index') }}" class="mb-6">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-gray-700">Quick Filter: Website Registrations</label>
                                @php
                                    $pendingWebsiteCount = 0;
                                    // Institute admins don't see website registrations (they only see their own students)
                                    if (auth()->user() && !auth()->user()->isInstituteAdmin()) {
                                        $pendingWebsiteCount = \App\Models\Student::whereNull('created_by')
                                            ->where('status', 'pending')
                                            ->when(!auth()->user()->isSuperAdmin(), function($q) {
                                                $instituteId = session('current_institute_id');
                                                if ($instituteId) {
                                                    $q->where('institute_id', $instituteId);
                                                }
                                            })
                                            ->count();
                                    }
                                @endphp
                                @if($pendingWebsiteCount > 0)
                                    <a href="{{ route('admin.students.index', ['registration_type' => 'website', 'status' => 'pending']) }}" 
                                       class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                                        {{ $pendingWebsiteCount }} Pending Website Registrations →
                                    </a>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600">
                                @if(auth()->user() && auth()->user()->isInstituteAdmin())
                                    View and manage students you have registered.
                                @else
                                    Use the filters below to view all students, or filter by registration type (Website/Guest), status, and institute.
                                @endif
                            </p>
                        </div>
                        <div class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input
                                    type="text"
                                    id="search"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search by name, registration number, roll number, email, or phone..."
                                    class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            @if(auth()->user() && auth()->user()->isSuperAdmin())
                            <div class="w-full md:w-48">
                                <label for="institute_id" class="block text-sm font-medium text-gray-700 mb-1">Institute</label>
                                <select
                                    id="institute_id"
                                    name="institute_id"
                                    class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                >
                                    <option value="">All Institutes</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{ $institute->id }}" {{ (string)request('institute_id') === (string)$institute->id ? 'selected' : '' }}>
                                            {{ $institute->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="w-full md:w-40">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select
                                    id="status"
                                    name="status"
                                    class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                >
                                    <option value="">All Status</option>
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-full md:w-40">
                                <label for="registration_type" class="block text-sm font-medium text-gray-700 mb-1">Registration Type</label>
                                <select
                                    id="registration_type"
                                    name="registration_type"
                                    class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                >
                                    <option value="">All Types</option>
                                    <option value="website" {{ request('registration_type') === 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="guest" {{ request('registration_type') === 'guest' ? 'selected' : '' }}>Guest/Admin</option>
                                </select>
                            </div>

                            <div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Students Card View (All Screens) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @forelse($students as $student)
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h4 class="text-base font-semibold text-gray-900">{{ $student->name }}</h4>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full flex-shrink-0
                                                @if($student->status === 'active')
                                                    bg-green-100 text-green-800
                                                @elseif($student->status === 'pending')
                                                    bg-yellow-100 text-yellow-800
                                                @else
                                                    bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <p class="text-xs font-medium text-indigo-600">
                                                Reg: {{ $student->registration_number ?? 'N/A' }}
                                            </p>
                                            @if($student->roll_number)
                                                <p class="text-xs text-gray-500">Roll: {{ $student->roll_number }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center">
                                        <span class="text-xs font-medium text-gray-500 w-20 flex-shrink-0">Type:</span>
                                        <span class="text-sm">
                                            @if($student->created_by)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Guest
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    Website
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-xs font-medium text-gray-500 w-20 flex-shrink-0">Institute:</span>
                                        <span class="text-sm text-gray-900 flex-1">
                                            @if($student->institute)
                                                @php
                                                    $isParamedical = \Illuminate\Support\Str::contains(
                                                        \Illuminate\Support\Str::lower($student->institute->name),
                                                        'paramedical'
                                                    );
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    {{ $isParamedical ? 'bg-green-50 text-green-800' : 'bg-blue-50 text-blue-800' }}">
                                                    {{ $student->institute->name }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">N/A</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-xs font-medium text-gray-500 w-20 flex-shrink-0">Course:</span>
                                        <span class="text-sm text-gray-900 flex-1">{{ $student->course->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-start pt-2 border-t border-gray-100">
                                        <span class="text-xs font-medium text-gray-500 w-20 flex-shrink-0">Email:</span>
                                        <span class="text-sm text-gray-900 flex-1 break-all">{{ $student->email ?? 'N/A' }}</span>
                                    </div>
                                    @if($student->phone)
                                    <div class="flex items-center">
                                        <span class="text-xs font-medium text-gray-500 w-20 flex-shrink-0">Phone:</span>
                                        <span class="text-sm text-gray-900">{{ $student->phone }}</span>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.students.show', $student->id) }}" 
                                           class="flex-1 px-3 py-2 text-xs font-medium text-center text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100 transition">
                                            View
                                        </a>
                                        @if(auth()->user() && auth()->user()->isSuperAdmin())
                                            <a href="{{ route('admin.students.edit', $student->id) }}" 
                                               class="flex-1 px-3 py-2 text-xs font-medium text-center text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition">
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No students found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by registering a new student.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.students.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        + Register New Student
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($students->hasPages())
                        <div class="mt-6">
                            {{ $students->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

