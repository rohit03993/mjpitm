@extends('layouts.paramedical')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-4xl font-bold text-green-900 mb-6">About {{ $institute->name }}</h1>
        
        <div class="prose max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed mb-6">
                {{ $institute->description ?? 'Mahatma Jyotiba Phule Institute of Paramedical Science is dedicated to excellence in healthcare education and training future healthcare professionals.' }}
            </p>
            
            <h2 class="text-2xl font-semibold text-green-900 mt-8 mb-4">Our Mission</h2>
            <p class="text-gray-700 mb-6">
                To provide quality paramedical and health science education that prepares students to serve the healthcare industry with competence and compassion. We are committed to producing skilled healthcare professionals.
            </p>
            
            <h2 class="text-2xl font-semibold text-green-900 mt-8 mb-4">Our Vision</h2>
            <p class="text-gray-700 mb-6">
                To be a leading institute in paramedical and health science education, recognized for excellence in training healthcare professionals who contribute to the well-being of society.
            </p>
        </div>
    </div>
</div>
@endsection
