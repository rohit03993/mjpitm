<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | {{ config('app.name', 'MJPI Institutions') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Simple header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" class="text-lg sm:text-xl font-bold text-gray-900">
                        Mahatma Jyotiba Phule Institutes
                    </a>
                </div>
                <a href="{{ route('home') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    ← Back to Website
                </a>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">
                    Choose Login Type
                </h1>
                <p class="text-gray-600 mb-10 text-sm sm:text-base">
                    Please select how you want to log in to the system.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Admin Login -->
                    <div class="bg-white border border-purple-100 rounded-xl shadow-sm hover:shadow-md transition p-6 flex flex-col">
                        <div class="mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-1">
                                Admin
                            </h2>
                            <p class="text-sm text-gray-600">
                                Full system access, manage all institutes, users, and system settings.
                            </p>
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('superadmin.login') }}"
                               class="inline-flex items-center px-5 py-2.5 rounded-md bg-purple-600 text-white text-sm font-semibold hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Admin Login
                            </a>
                        </div>
                    </div>

                    <!-- Staff Login -->
                    <div class="bg-white border border-blue-100 rounded-xl shadow-sm hover:shadow-md transition p-6 flex flex-col">
                        <div class="mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-1">
                                Staff / Admin
                            </h2>
                            <p class="text-sm text-gray-600">
                                Institute Admin and Staff - Add students and manage your institute operations.
                            </p>
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('staff.login') }}"
                               class="inline-flex items-center px-5 py-2.5 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Staff Login
                            </a>
                        </div>
                    </div>

                    <!-- Student Login -->
                    <div class="bg-white border border-amber-100 rounded-xl shadow-sm hover:shadow-md transition p-6 flex flex-col">
                        <div class="mb-4">
                            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-1">
                                Student
                            </h2>
                            <p class="text-sm text-gray-600">
                                Access your student portal to view results, fees, and personal information.
                            </p>
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('student.login') }}"
                               class="inline-flex items-center px-5 py-2.5 rounded-md bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                Student Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-xs sm:text-sm text-gray-500 text-center">
                &copy; {{ date('Y') }} Mahatma Jyotiba Phule Institutes. All rights reserved.
            </div>
        </footer>
    </div>
</body>
</html>


