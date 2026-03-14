<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Password Reset Link - Student - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50">
    <div class="min-h-screen flex items-center justify-center py-4 sm:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6 bg-white rounded-lg shadow-lg p-6 sm:p-8">
            <div class="text-center">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Password reset</h2>
                @if (session('status'))
                    <p class="mt-2 text-sm text-gray-600">{{ session('status') }}</p>
                @endif
            </div>
            @if (session('reset_link'))
                <div class="rounded-md bg-amber-50 border border-amber-200 p-4">
                    <p class="text-sm text-amber-800 mb-3">Use this link within 60 minutes. Do not share it.</p>
                    <a href="{{ session('reset_link') }}" class="text-sm font-medium text-amber-700 underline break-all">
                        {{ session('reset_link') }}
                    </a>
                </div>
            @else
                <p class="text-sm text-gray-600">If an account exists with that identifier, check the message above or request a new link.</p>
            @endif
            <div class="text-center pt-2">
                <a href="{{ route('student.login') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
