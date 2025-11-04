<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <h1 class="text-2xl font-bold text-center mb-6">Student Login</h1>
            <p class="text-gray-600 text-center mb-6">Student login will be implemented soon.</p>
            <a href="{{ route('landing') }}" class="block text-center text-blue-600 hover:text-blue-800">
                ← Back to Home
            </a>
        </div>
    </div>
</body>
</html>
