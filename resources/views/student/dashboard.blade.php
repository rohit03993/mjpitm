<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg border-b-4 border-amber-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center space-x-3">
                            <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h1 class="text-xl font-bold text-gray-900">
                                Student Portal
                            </h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                            <p class="text-xs text-gray-500">{{ $student->registration_number ?? '—' }}</p>
                        </div>
                        <form method="POST" action="{{ route('student.logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Welcome Message -->
                <div class="bg-gradient-to-r from-amber-500 to-yellow-500 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-8 text-white">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold mb-2">Welcome, {{ $student->name }}!</h2>
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full">
                                        <strong>Registration:</strong> {{ $student->registration_number ?? '—' }}
                                    </span>
                                    @if($student->roll_number)
                                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full">
                                        <strong>Roll No:</strong> {{ $student->roll_number }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Information Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Course Information -->
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-blue-500">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Course Information</h3>
                            </div>
                            <div class="space-y-2">
                                <p class="text-gray-700">
                                    <span class="font-medium">Course:</span> 
                                    <span class="text-gray-900">{{ $student->course->name ?? 'N/A' }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Semester:</span> 
                                    <span class="text-gray-900">{{ $student->current_semester ?? 'N/A' }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Admission Year:</span> 
                                    <span class="text-gray-900">{{ $student->admission_year ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Status -->
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-green-500">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Fee Status</h3>
                            </div>
                            <div class="space-y-2">
                                <p class="text-gray-700">
                                    <span class="font-medium">Total Fees:</span> 
                                    <span class="text-gray-900">{{ $student->fees->count() }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Verified:</span> 
                                    <span class="text-green-600 font-semibold">{{ $student->fees->where('status', 'verified')->count() }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Pending:</span> 
                                    <span class="text-yellow-600 font-semibold">{{ $student->fees->where('status', 'pending_verification')->count() }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Results Status -->
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-purple-500">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Results Status</h3>
                            </div>
                            <div class="space-y-2">
                                <p class="text-gray-700">
                                    <span class="font-medium">Total Results:</span> 
                                    <span class="text-gray-900">{{ $student->results->count() }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Published:</span> 
                                    <span class="text-green-600 font-semibold">{{ $student->results->where('status', 'published')->count() }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Pending:</span> 
                                    <span class="text-yellow-600 font-semibold">{{ $student->results->where('status', 'pending_verification')->count() }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="#" class="block p-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg hover:shadow-md transition border border-indigo-100">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-indigo-900 text-lg">View Results</h4>
                                        <p class="text-sm text-indigo-700 mt-1">Check your exam results</p>
                                        <span class="text-xs text-indigo-600 mt-1 inline-block">Coming Soon</span>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg hover:shadow-md transition border border-green-100">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-green-900 text-lg">View Fees</h4>
                                        <p class="text-sm text-green-700 mt-1">Check your fee payment status</p>
                                        <span class="text-xs text-green-600 mt-1 inline-block">Coming Soon</span>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('registration.form') }}" class="block p-6 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-lg hover:shadow-md transition border border-amber-200">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-amber-900 text-lg">Registration Form</h4>
                                        <p class="text-sm text-amber-700 mt-1">Download registration form</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('home') }}" class="block p-6 bg-gradient-to-br from-gray-50 to-slate-50 rounded-lg hover:shadow-md transition border border-gray-200">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gray-500 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-lg">Institute Website</h4>
                                        <p class="text-sm text-gray-700 mt-1">Visit the institute website</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

