@extends('layouts.tech')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-4xl font-bold text-blue-900 mb-6">About {{ $institute->name }}</h1>
        
        <div class="prose max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed mb-6">
                {{ $institute->description ?? 'Mahatma Jyotiba Phule Institute of Technology & Management is a premier institution dedicated to providing excellence in technical and management education.' }}
            </p>
            
            <h2 class="text-2xl font-semibold text-blue-900 mt-8 mb-4">Our Mission</h2>
            <p class="text-gray-700 mb-6">
                To provide quality technical and management education that prepares students for successful careers in the industry. We are committed to nurturing talent and fostering innovation.
            </p>
            
            <h2 class="text-2xl font-semibold text-blue-900 mt-8 mb-4">Our Vision</h2>
            <p class="text-gray-700 mb-6">
                To be a leading institute in technical and management education, recognized for excellence in teaching, research, and industry collaboration.
            </p>
        </div>
    </div>
</div>
@endsection
