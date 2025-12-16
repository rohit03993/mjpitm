<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                {{ __('Guest Dashboard') }}
            </h2>
            <div class="text-xs sm:text-sm text-gray-600">
                Logged in as: <span class="font-semibold text-gray-800">{{ Auth::user()->name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 lg:py-12">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 overflow-hidden shadow-lg sm:rounded-xl mb-4 sm:mb-6">
                <div class="p-5 sm:p-6 lg:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-2 leading-tight">
                                Welcome, {{ Auth::user()->name }}!
                            </h3>
                            <p class="text-sm sm:text-base text-blue-100 leading-relaxed">
                                You are logged in as <strong class="text-white font-semibold">Guest (Channel Partner)</strong>
                                @if(Auth::user()->institute)
                                    at <strong class="text-white font-semibold">{{ Auth::user()->institute->name }}</strong>
                                @endif
                            </p>
                        </div>
                        <div class="hidden sm:flex flex-shrink-0">
                            <div class="w-16 h-16 lg:w-20 lg:h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center border-2 border-white border-opacity-30 shadow-lg">
                                <svg class="w-8 h-8 lg:w-10 lg:h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6">
                <!-- Total Students Added -->
                <div class="bg-white overflow-hidden shadow-md hover:shadow-lg transition-all duration-200 sm:rounded-lg border border-gray-100">
                    <div class="p-4 sm:p-5 lg:p-6">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <div class="flex-shrink-0 bg-blue-500 rounded-xl p-2.5 sm:p-3 shadow-sm">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate mb-1">Total Students</dt>
                                <dd class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-none">{{ $totalStudents }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Students -->
                <div class="bg-white overflow-hidden shadow-md hover:shadow-lg transition-all duration-200 sm:rounded-lg border border-gray-100">
                    <div class="p-4 sm:p-5 lg:p-6">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <div class="flex-shrink-0 bg-green-500 rounded-xl p-2.5 sm:p-3 shadow-sm">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate mb-1">Active Students</dt>
                                <dd class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-none">{{ $activeStudents }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Students -->
                <div class="bg-white overflow-hidden shadow-md hover:shadow-lg transition-all duration-200 sm:rounded-lg border border-gray-100">
                    <div class="p-4 sm:p-5 lg:p-6">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-xl p-2.5 sm:p-3 shadow-sm">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate mb-1">Pending Approval</dt>
                                <dd class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-none">{{ $pendingStudents }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verified Fees Collected -->
                <div class="bg-white overflow-hidden shadow-md hover:shadow-lg transition-all duration-200 sm:rounded-lg border border-gray-100">
                    <div class="p-4 sm:p-5 lg:p-6">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <div class="flex-shrink-0 bg-emerald-500 rounded-xl p-2.5 sm:p-3 shadow-sm">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate mb-1">Fees Collected</dt>
                                <dd class="text-lg sm:text-xl lg:text-2xl font-bold text-emerald-600 leading-none">₹{{ number_format($totalFeesCollected, 2) }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Summary -->
            @if($pendingFees > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 sm:p-5 mb-4 sm:mb-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm sm:text-base text-yellow-800">
                        You have <strong class="font-bold">₹{{ number_format($pendingFees, 2) }}</strong> in payments pending Admin verification.
                    </p>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg mb-4 sm:mb-6 border border-gray-100">
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4 sm:mb-5">Quick Actions</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        <a href="{{ route('admin.students.create') }}" class="group p-4 sm:p-5 bg-blue-50 rounded-xl hover:bg-blue-100 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] border border-blue-100 hover:border-blue-200">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-500 rounded-xl flex items-center justify-center mb-3 group-hover:bg-blue-600 transition-colors shadow-sm">
                                    <svg class="h-6 w-6 sm:h-7 sm:w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <h4 class="text-xs sm:text-sm font-bold text-blue-900 mb-1">Add New Student</h4>
                                <p class="text-xs text-blue-700 leading-tight">Register a new student</p>
                            </div>
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="group p-4 sm:p-5 bg-green-50 rounded-xl hover:bg-green-100 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] border border-green-100 hover:border-green-200">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-green-500 rounded-xl flex items-center justify-center mb-3 group-hover:bg-green-600 transition-colors shadow-sm">
                                    <svg class="h-6 w-6 sm:h-7 sm:w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h4 class="text-xs sm:text-sm font-bold text-green-900 mb-1">View My Students</h4>
                                <p class="text-xs text-green-700 leading-tight">See all students you added</p>
                            </div>
                        </a>
                        <a href="{{ route('admin.fees.index') }}" class="group p-4 sm:p-5 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] border border-indigo-100 hover:border-indigo-200">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-500 rounded-xl flex items-center justify-center mb-3 group-hover:bg-indigo-600 transition-colors shadow-sm">
                                    <svg class="h-6 w-6 sm:h-7 sm:w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-xs sm:text-sm font-bold text-indigo-900 mb-1">Manage Fees</h4>
                                <p class="text-xs text-indigo-700 leading-tight">View & add fee payments</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Students by Institute -->
            @if(count($institutes) > 0)
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg mb-4 sm:mb-6 border border-gray-100">
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4 sm:mb-5">Students by Institute</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($institutes as $instituteData)
                        <div class="border border-gray-200 rounded-xl p-4 sm:p-5 bg-gradient-to-br from-gray-50 to-white hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm sm:text-base font-bold text-gray-900 truncate flex-1 mr-2">{{ $instituteData['institute']->name }}</h4>
                                <span class="px-2.5 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full border border-blue-200 flex-shrink-0">
                                    {{ $instituteData['students_count'] }} students
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-600">
                                <span class="font-bold text-gray-900">{{ $instituteData['active_count'] }}</span> active students
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Students -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg border border-gray-100">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-5 sm:mb-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Recent Students You Added</h3>
                        <a href="{{ route('admin.students.index') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-semibold flex items-center group">
                            View All
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    
                    <!-- Students Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 lg:gap-6">
                        @forelse($recentStudents as $student)
                            <a href="{{ route('admin.students.show', $student->id) }}" class="block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-200 border border-gray-200 overflow-hidden group">
                                <!-- Card Header -->
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-4 sm:px-5 sm:py-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                                            <!-- Student Avatar/Icon -->
                                            <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center border-2 border-white border-opacity-30 shadow-sm">
                                                @if($student->photo)
                                                    <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="w-full h-full object-cover rounded-full">
                                                @else
                                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-sm sm:text-base font-bold text-white truncate group-hover:text-yellow-200 transition-colors leading-tight">
                                                    {{ $student->name }}
                                                </h4>
                                                <p class="text-xs text-blue-100 truncate mt-0.5">
                                                    {{ $student->registration_number ?? '—' }}
                                                </p>
                                            </div>
                                        </div>
                                        <!-- Status Badge -->
                                        <span class="flex-shrink-0 px-2.5 py-1 inline-flex text-xs font-semibold rounded-full shadow-sm
                                            @if($student->status === 'active')
                                                bg-green-100 text-green-800 border border-green-200
                                            @elseif($student->status === 'pending')
                                                bg-yellow-100 text-yellow-800 border border-yellow-200
                                            @else
                                                bg-red-100 text-red-800 border border-red-200
                                            @endif">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="p-4 sm:p-5">
                                    <div class="space-y-3.5">
                                        <!-- Institute -->
                                        @if($student->institute)
                                        <div class="flex items-start justify-between gap-2">
                                            <span class="text-xs font-medium text-gray-500 flex items-center flex-shrink-0">
                                                <svg class="w-4 h-4 mr-1.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                Institute
                                            </span>
                                            <span class="text-xs font-semibold text-gray-900 truncate text-right flex-1 ml-2">
                                                {{ Str::limit($student->institute->name, 30) }}
                                            </span>
                                        </div>
                                        @endif

                                        <!-- Course -->
                                        <div class="flex items-start justify-between gap-2">
                                            <span class="text-xs font-medium text-gray-500 flex items-center flex-shrink-0">
                                                <svg class="w-4 h-4 mr-1.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                Course
                                            </span>
                                            <span class="text-xs font-semibold text-gray-900 truncate text-right flex-1 ml-2">
                                                {{ Str::limit($student->course->name ?? 'N/A', 30) }}
                                            </span>
                                        </div>

                                        <!-- Added Date -->
                                        <div class="flex items-center justify-between border-t border-gray-100 pt-3.5">
                                            <span class="text-xs text-gray-400 font-medium">Added</span>
                                            <span class="text-xs font-semibold text-gray-600">
                                                {{ $student->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- View Button -->
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <div class="flex items-center justify-center text-blue-600 group-hover:text-blue-700 transition-colors">
                                            <span class="text-xs sm:text-sm font-semibold">View Details</span>
                                            <svg class="w-4 h-4 ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="col-span-full bg-white rounded-lg shadow-md p-8 sm:p-12 text-center">
                                <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h3 class="mt-4 text-base sm:text-lg font-medium text-gray-900">No students yet</h3>
                                <p class="mt-2 text-sm text-gray-500">Get started by adding your first student.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.students.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Add Student
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

