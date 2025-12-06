@php
    // Determine layout based on institute ID
    $instituteId = $student->institute_id ?? 1;
    $layoutName = ($instituteId == 1) ? 'layouts.tech' : 'layouts.paramedical';
@endphp
@extends($layoutName)

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-8 text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <!-- Success Message -->
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Registration Successful!</h1>
                <p class="text-lg text-gray-600 mb-6">
                    Thank you for registering with {{ $student->institute->name ?? 'the institute' }}.
                </p>

                <!-- Registration Number -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <p class="text-sm text-gray-600 mb-2">Your Registration Number:</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $student->registration_number }}</p>
                </div>

                <!-- Important Information -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6 text-left">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-3">Important Information</h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <span>Your registration is currently <strong>pending</strong> and awaiting approval from the administrator.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <span>Once approved, you will receive a roll number and can access your student dashboard.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <span>Please save your registration number for future reference. You can use it along with your email and password to login once your account is activated.</span>
                        </li>
                    </ul>
                </div>

                <!-- Next Steps -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Next Steps</h3>
                    <ol class="space-y-2 text-sm text-gray-700 list-decimal list-inside">
                        <li>Wait for administrative approval of your registration</li>
                        <li>You will be notified once your account is activated</li>
                        <li>Login using your email address and password</li>
                        <li>Access your student dashboard and complete your profile</li>
                    </ol>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Return to Home
                    </a>
                    <a href="{{ route('login.options') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white {{ $instituteId == 1 ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }}">
                        Go to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

