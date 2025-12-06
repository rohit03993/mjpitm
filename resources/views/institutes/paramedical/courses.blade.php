@extends('layouts.paramedical')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-green-900 mb-4">Our Courses</h1>
        <p class="text-gray-600 text-lg">Explore our range of paramedical and health science programs</p>
    </div>
    
    @if(isset($courses) && $courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition p-6">
                    <h3 class="text-xl font-semibold text-green-900 mb-2">{{ $course->name }}</h3>
                    <p class="text-gray-600 mb-2">Code: {{ $course->code }}</p>
                    <p class="text-gray-600 mb-4">Duration: {{ $course->formatted_duration }}</p>
                    @if($course->description)
                        <p class="text-gray-700 mb-4">{{ Str::limit($course->description, 100) }}</p>
                    @endif
                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                        {{ $course->status }}
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <p class="text-gray-600 text-lg">Courses will be available soon. Please check back later.</p>
        </div>
    @endif
</div>
@endsection
