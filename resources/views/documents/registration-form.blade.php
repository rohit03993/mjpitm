<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Form - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg border-b-4 border-blue-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900">
                            Mahatma Jyotiba Phule Institutes
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                        @else
                            <a href="{{ route('login.options') }}" class="text-gray-700 hover:text-gray-900">Login</a>
                        @endauth
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-gray-900">Home</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                    <h1 class="text-3xl font-bold text-white mb-2">Student Registration Form</h1>
                    <p class="text-blue-100">Download the registration form to apply for admission</p>
                </div>

                <!-- Content -->
                <div class="p-8">
                    @if(session('error'))
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($formExists)
                        <div class="text-center mb-8">
                            <div class="inline-block bg-green-100 rounded-full p-4 mb-4">
                                <svg class="w-16 h-16 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900 mb-2">Registration Form Available</h2>
                            <p class="text-gray-600 mb-6">Click the button below to download the student registration form in PDF format.</p>
                            
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="{{ route('documents.download.registration') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download PDF
                                </a>
                                <a href="{{ route('documents.view.registration') }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View in Browser
                                </a>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Instructions:</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Download the registration form by clicking the "Download PDF" button above</li>
                                <li>Print the form or fill it digitally</li>
                                <li>Complete all required fields accurately</li>
                                <li>Attach necessary documents (photo, certificates, etc.)</li>
                                <li>Submit the completed form to the institute office or through the online portal</li>
                            </ol>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="inline-block bg-yellow-100 rounded-full p-4 mb-4">
                                <svg class="w-16 h-16 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900 mb-2">Registration Form Not Available</h2>
                            <p class="text-gray-600 mb-6">The registration form is currently being updated. Please check back later or contact the institute office.</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                                ← Back to Home
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Need Help?</h3>
                <p class="text-blue-800 mb-4">If you have any questions about the registration process, please contact:</p>
                <ul class="text-blue-700 space-y-1">
                    <li>📧 Email: info@mjpitm.in / info@mjpips.in</li>
                    <li>📞 Phone: Contact your institute office</li>
                    <li>📍 Visit: Institute office during working hours</li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>

