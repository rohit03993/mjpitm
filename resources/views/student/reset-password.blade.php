<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Set new password - Student - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50">
    <div class="min-h-screen flex items-center justify-center py-4 sm:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6 bg-white rounded-lg shadow-lg p-6 sm:p-8">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 text-center">Set new password</h2>
            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-md text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('student.password.update') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New password</label>
                    <input id="password" name="password" type="password" required minlength="8"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-amber-500 focus:border-amber-500">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-amber-500 focus:border-amber-500">
                </div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                    Reset password
                </button>
            </form>
            <div class="text-center">
                <a href="{{ route('student.login') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
