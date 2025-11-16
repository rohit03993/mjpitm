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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Login -->
                    <div class="bg-white border border-yellow-100 rounded-xl shadow-sm hover:shadow-md transition p-6 flex flex-col">
                        <div class="mb-4">
                            <h2 class="text-lg font-semibold text-gray-900 mb-1">
                                Student Login
                            </h2>
                            <p class="text-sm text-gray-600">
                                Access the student portal to view your details and information.
                            </p>
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('student.login') }}"
                               class="inline-flex items-center px-5 py-2.5 rounded-md bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                Student Login
                            </a>
                        </div>
                    </div>

                    <!-- Staff / Admin Login -->
                    <div class="bg-white border border-blue-100 rounded-xl shadow-sm hover:shadow-md transition p-6 flex flex-col">
                        <div class="mb-4">
                            <h2 class="text-lg font-semibold text-gray-900 mb-1">
                                Staff / Admin Login
                            </h2>
                            <p class="text-sm text-gray-600">
                                For Super Admin, Institute Admin and authorized staff members.
                            </p>
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center px-5 py-2.5 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Staff Login
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


