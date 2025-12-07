@extends('layouts.tech')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('courses') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>Back to All Categories
        </a>
    </div>

    <!-- Category Header -->
    <div class="text-center mb-12">
        @if($category->image)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full max-w-2xl mx-auto h-64 object-cover rounded-lg shadow-lg">
            </div>
        @endif
        <h1 class="text-4xl font-bold text-blue-900 mb-4">{{ $category->name }}</h1>
        <p class="text-gray-600 text-lg max-w-3xl mx-auto">
            @if($category->description)
                {{ $category->description }}
            @else
                {!! \App\Http\Controllers\LandingPageController::getCategoryDescription($category->name) !!}
            @endif
        </p>
    </div>
    
    @if(isset($courses) && $courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden border border-gray-200">
                    <!-- Course Image -->
                    @if($course->image)
                        <div class="h-48 overflow-hidden">
                            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center">
                            <i class="fas fa-book text-5xl text-white opacity-80"></i>
                        </div>
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-blue-900 mb-2">{{ $course->name }}</h3>
                    <p class="text-gray-600 mb-2">Code: {{ $course->code }}</p>
                    <p class="text-gray-600 mb-2">Duration: {{ $course->formatted_duration }}</p>
                    
                    <!-- Fee Information -->
                    <div class="my-4 space-y-2">
                        @php
                            $tuitionFee = $course->tuition_fee ?? 0;
                            // Always set registration fee to 1000 if null or 0
                            $registrationFee = ($course->registration_fee && $course->registration_fee > 0) ? $course->registration_fee : 1000;
                            $totalFee = $tuitionFee + $registrationFee;
                        @endphp
                        @if($tuitionFee > 0)
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold">Tuition Fee:</span> ₹{{ number_format($tuitionFee, 2) }}
                            </p>
                        @endif
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold">Registration Fee:</span> ₹{{ number_format($registrationFee, 2) }}
                        </p>
                        <p class="text-lg font-bold text-blue-900">
                            <span class="font-semibold">Total Fee:</span> ₹{{ number_format($totalFee, 2) }}
                        </p>
                    </div>
                    
                    @if($course->description)
                        <p class="text-gray-700 mb-4 text-sm">{{ Str::limit($course->description, 100) }}</p>
                    @endif
                    
                    <!-- Apply Now Button -->
                        <a href="{{ route('public.registration', ['category' => Str::slug($category->name), 'course' => Str::slug($course->name)]) }}" 
                           class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                            Apply Now
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <p class="text-gray-600 text-lg">No courses available in this category yet. Please check back later.</p>
        </div>
    @endif
</div>
@endsection

