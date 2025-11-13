<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-gray-900">
                                Student Dashboard
                            </h1>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-700 mr-4">
                            {{ $student->name }}
                        </span>
                        <form method="POST" action="{{ route('student.logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900hover:text-white">
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h2 class="text-2xl font-bold mb-2">Welcome, {{ $student->name }}!</h2>
                        <p class="text-gray-600">
                            Roll Number: {{ $student->roll_number }}
                        </p>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Course Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Course Information</h3>
                        <p class="text-gray-600">
                            <strong>Course:</strong> {{ $student->course->name ?? 'N/A' }}
                        </p>
                        <p class="text-gray-600">
                            <strong>Semester:</strong> {{ $student->current_semester }}
                        </p>
                        <p class="text-gray-600">
                            <strong>Admission Year:</strong> {{ $student->admission_year }}
                        </p>
                        </div>
                    </div>

                    <!-- Fee Status -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Fee Status</h3>
                            <p class="text-gray-600">
                                Total Fees: {{ $student->fees->count() }}
                            </p>
                            <p class="text-gray-600">
                                Verified Fees: {{ $student->fees->where('status', 'verified')->count() }}
                            </p>
                            <p class="text-gray-600">
                                Pending Fees: {{ $student->fees->where('status', 'pending_verification')->count() }}
                            </p>
                        </div>
                    </div>

                    <!-- Results Status -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Results Status</h3>
                            <p class="text-gray-600">
                                Total Results: {{ $student->results->count() }}
                            </p>
                            <p class="text-gray-600">
                                Published Results: {{ $student->results->where('status', 'published')->count() }}
                            </p>
                            <p class="text-gray-600">
                                Pending Results: {{ $student->results->where('status', 'pending_verification')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="block p-4 bg-indigo-50 rounded-lg">
                                <h4 class="font-medium text-indigo-900">View Results</h4>
                                <p class="text-sm text-indigo-700">Check your exam results (Coming Soon)</p>
                            </div>
                            <div class="block p-4 bg-green-50 rounded-lg">
                                <h4 class="font-medium text-green-900">View Fees</h4>
                                <p class="text-sm text-green-700">Check your fee payment status (Coming Soon)</p>
                            </div>
                            <div class="block p-4 bg-blue-50 rounded-lg">
                                <h4 class="font-medium text-blue-900">My Profile</h4>
                                <p class="text-sm text-blue-700">View and update your profile (Coming Soon)</p>
                            </div>
                            <a href="{{ route('home') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <h4 class="font-medium text-gray-900">Institute Website</h4>
                                <p class="text-sm text-gray-700">Visit the institute website</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

