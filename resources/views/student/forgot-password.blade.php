<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Forgot Password - Student - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50">
    <div class="min-h-screen flex items-center justify-center py-4 sm:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6 sm:space-y-8">
            <div>
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-amber-500 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-center text-2xl sm:text-3xl font-extrabold text-gray-900">
                    Forgot Password
                </h2>
                <p class="mt-2 text-center text-xs sm:text-sm text-gray-600 px-2">
                    Enter your registration / roll number to receive a password reset link
                </p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
                    <p class="text-sm text-green-800">
                        {{ session('status') }}
                    </p>
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ __('Whoops! Something went wrong.') }}
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form class="mt-6 sm:mt-8 space-y-4 sm:space-y-6 bg-white rounded-lg shadow-lg p-4 sm:p-6 sm:p-8" action="{{ route('student.password.email') }}" method="POST">
                @csrf

                <div>
                    <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">
                        Registration / Roll Number
                    </label>
                    <input 
                        id="identifier" 
                        name="identifier" 
                        type="text" 
                        required 
                        autofocus
                        value="{{ old('identifier') }}"
                        class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 bg-white rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-base sm:text-sm" 
                        placeholder="Enter your registration or roll number" 
                    />
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-base sm:text-sm font-semibold rounded-md text-white bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 shadow-lg transition-all"
                    >
                        {{ __('Send Password Reset Link') }}
                    </button>
                </div>

                <div class="text-center pt-2">
                    <a href="{{ route('student.login') }}" class="text-sm text-gray-600 hover:text-gray-900 inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

