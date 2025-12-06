<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | {{ config('app.name', 'MJPI Institutions') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">
    <div class="min-h-screen flex flex-col">
        <!-- Simple header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-3 sm:py-4">
                <div class="flex items-center justify-between">
                    <a href="{{ route('home') }}" class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 truncate flex-1 min-w-0">
                        Mahatma Jyotiba Phule Institutes
                    </a>
                    <a href="{{ route('home') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center ml-3 flex-shrink-0">
                        <svg class="w-4 h-4 mr-1 hidden sm:inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span class="hidden sm:inline">Back to Website</span>
                        <span class="sm:hidden">Home</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-6 sm:py-10 lg:py-16">
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-2 sm:mb-3">
                        Choose Login Type
                    </h1>
                    <p class="text-gray-600 text-sm sm:text-base px-2">
                        Please select how you want to log in to the system.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 lg:gap-6 max-w-6xl mx-auto">
                    <!-- Admin Login -->
                    <a href="{{ route('superadmin.login') }}" class="bg-white border-2 border-purple-200 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 p-5 sm:p-6 flex flex-col group transform hover:scale-[1.02] active:scale-[0.98]">
                        <div class="mb-4">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center mb-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">
                                Admin
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                                Full system access, manage all institutes, users, and system settings.
                            </p>
                        </div>
                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <div class="inline-flex items-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-semibold w-full justify-center group-hover:from-purple-700 group-hover:to-indigo-700 transition-all">
                                Admin Login
                                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- Guest Login (Channel Partners) -->
                    <a href="{{ route('staff.login') }}" class="bg-white border-2 border-blue-200 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 p-5 sm:p-6 flex flex-col group transform hover:scale-[1.02] active:scale-[0.98]">
                        <div class="mb-4">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mb-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">
                                Guest
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                                Channel Partners - Register students and collect fees on behalf of institutes.
                            </p>
                        </div>
                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <div class="inline-flex items-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold w-full justify-center group-hover:from-blue-700 group-hover:to-indigo-700 transition-all">
                                Guest Login
                                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- Student Login -->
                    <a href="{{ route('student.login') }}" class="bg-white border-2 border-amber-200 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 p-5 sm:p-6 flex flex-col group transform hover:scale-[1.02] active:scale-[0.98]">
                        <div class="mb-4">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mb-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">
                                Student
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                                Access your student portal to view results, fees, and personal information.
                            </p>
                        </div>
                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <div class="inline-flex items-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-semibold w-full justify-center group-hover:from-amber-600 group-hover:to-orange-600 transition-all">
                                Student Login
                                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white mt-8 sm:mt-12">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-xs sm:text-sm text-gray-500 text-center">
                &copy; {{ date('Y') }} Mahatma Jyotiba Phule Institutes. All rights reserved.
            </div>
        </footer>
    </div>
</body>
</html>


