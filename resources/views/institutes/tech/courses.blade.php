@extends('layouts.tech')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-blue-900 mb-4">Our Courses</h1>
        <p class="text-gray-600 text-lg">Explore our range of technical and management programs</p>
    </div>
    
    @if(isset($categories) && $categories->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                @if($category->active_courses_count > 0)
                    <a href="{{ route('courses.category', Str::slug($category->name)) }}" 
                       class="group bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 border-2 border-gray-200 hover:border-blue-500 transform hover:-translate-y-2 overflow-hidden">
                        <!-- Category Image -->
                        @if($category->image)
                            <div class="h-48 overflow-hidden">
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            </div>
                        @else
                            <div class="h-48 bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-6xl text-white opacity-80"></i>
                            </div>
                        @endif
                        <div class="p-6">
                            <!-- Category Header -->
                            <div class="mb-4">
                                <h2 class="text-2xl font-bold text-blue-900 mb-2 group-hover:text-blue-700 transition">{{ $category->name }}</h2>
                            </div>
                            
                            <!-- Category Description -->
                            <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                                @if($category->description)
                                    {{ Str::limit($category->description, 120) }}
                                @else
                                    {!! \App\Http\Controllers\LandingPageController::getCategoryDescription($category->name) !!}
                                @endif
                            </p>
                            
                            <!-- Course Count -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <span class="text-blue-600 font-semibold">
                                    <i class="fas fa-book mr-2"></i>{{ $category->active_courses_count }} {{ $category->active_courses_count == 1 ? 'Course' : 'Courses' }}
                                </span>
                                <span class="text-blue-600 group-hover:translate-x-2 transition-transform">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <p class="text-gray-600 text-lg">Courses will be available soon. Please check back later.</p>
        </div>
    @endif
</div>
@endsection
