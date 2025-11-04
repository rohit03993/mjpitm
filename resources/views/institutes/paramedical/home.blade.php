@extends('layouts.paramedical')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-green-900 via-green-800 to-green-900 text-white relative overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=1920&h=1080&fit=crop" alt="Healthcare professionals" class="w-full h-full object-cover opacity-20">
    </div>
    <div class="absolute inset-0 bg-black opacity-40"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="text-center lg:text-left">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                    {{ $institute->name ?? 'Mahatma Jyotiba Phule Institute of Paramedical Science' }}
                </h1>
                <p class="text-xl md:text-2xl mb-6 text-green-100 font-semibold">
                    Shaping Future Healthcare Professionals
                </p>
                <p class="text-lg mb-8 text-green-200 leading-relaxed">
                    Dedicated to excellence in healthcare education, training compassionate professionals who serve the community with skill and dedication.
                </p>
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('courses') }}" class="bg-white text-green-900 px-8 py-4 rounded-lg font-semibold hover:bg-green-50 transition shadow-xl text-lg w-full sm:w-auto text-center">
                        <i class="fas fa-user-md mr-2"></i>Explore Courses
                    </a>
                    <a href="{{ route('about') }}" class="bg-green-700 text-white px-8 py-4 rounded-lg font-semibold hover:bg-green-600 transition shadow-xl text-lg border-2 border-green-500 w-full sm:w-auto text-center">
                        <i class="fas fa-info-circle mr-2"></i>Learn More
                    </a>
                </div>
            </div>
            <div class="hidden lg:block">
                <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&h=600&fit=crop" alt="Medical students training" class="rounded-lg shadow-2xl">
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 100C120 80 240 40 360 30C480 20 600 40 720 50C840 60 960 60 1080 50C1200 40 1320 20 1380 10L1440 0V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- About Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">About Our Institute</h2>
            <div class="w-24 h-1 bg-green-600 mx-auto mb-6"></div>
            <p class="text-gray-700 text-lg max-w-3xl mx-auto font-medium">
                A premier institution dedicated to excellence in healthcare education and training
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <img src="https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=800&h=600&fit=crop" alt="Medical institute campus" class="rounded-lg shadow-lg w-full">
            </div>
            <div>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Our Mission</h3>
                <p class="text-gray-700 mb-6 leading-relaxed text-base">
                    To provide quality paramedical and health science education that prepares students to serve the healthcare industry with competence and compassion. We are committed to producing skilled healthcare professionals who make a positive impact on society.
                </p>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Our Vision</h3>
                <p class="text-gray-700 mb-6 leading-relaxed text-base">
                    To be a leading institute in paramedical and health science education, recognized for excellence in training healthcare professionals who contribute to the well-being of society through their knowledge, skills, and dedication.
                </p>
                <a href="{{ route('about') }}" class="inline-block bg-green-900 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-800 transition shadow-lg">
                    Read More <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-16 bg-green-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            <div class="bg-white p-6 md:p-8 rounded-lg text-center shadow-md hover:shadow-xl transition">
                <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-injured text-2xl md:text-3xl text-green-900"></i>
                </div>
                <h4 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">800+</h4>
                <p class="text-gray-700 font-medium">Students</p>
            </div>
            <div class="bg-white p-6 md:p-8 rounded-lg text-center shadow-md hover:shadow-xl transition">
                <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-graduation-cap text-2xl md:text-3xl text-green-900"></i>
                </div>
                <h4 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">8+</h4>
                <p class="text-gray-700 font-medium">Courses</p>
            </div>
            <div class="bg-white p-6 md:p-8 rounded-lg text-center shadow-md hover:shadow-xl transition">
                <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-md text-2xl md:text-3xl text-green-900"></i>
                </div>
                <h4 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">40+</h4>
                <p class="text-gray-700 font-medium">Faculty</p>
            </div>
            <div class="bg-white p-6 md:p-8 rounded-lg text-center shadow-md hover:shadow-xl transition">
                <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-hospital text-2xl md:text-3xl text-green-900"></i>
                </div>
                <h4 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">20+</h4>
                <p class="text-gray-700 font-medium">Hospitals</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Choose Us?</h2>
            <div class="w-24 h-1 bg-green-600 mx-auto mb-6"></div>
            <p class="text-gray-700 text-lg max-w-3xl mx-auto font-medium">
                Dedicated to excellence in healthcare education with comprehensive training programs
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="bg-white border-2 border-gray-100 p-6 md:p-8 rounded-lg shadow-md hover:shadow-xl transition text-center">
                <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user-md text-2xl md:text-3xl text-green-900"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">Healthcare Excellence</h3>
                <p class="text-gray-700 leading-relaxed text-sm md:text-base">
                    Comprehensive programs in paramedical and health sciences with focus on practical skills and clinical knowledge.
                </p>
            </div>
            
            <div class="bg-white border-2 border-gray-100 p-6 md:p-8 rounded-lg shadow-md hover:shadow-xl transition text-center">
                <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-chalkboard-teacher text-2xl md:text-3xl text-green-900"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">Expert Faculty</h3>
                <p class="text-gray-700 leading-relaxed text-sm md:text-base">
                    Experienced healthcare professionals and educators guiding your career with real-world insights and expertise.
                </p>
            </div>
            
            <div class="bg-white border-2 border-gray-100 p-6 md:p-8 rounded-lg shadow-md hover:shadow-xl transition text-center">
                <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-hospital text-2xl md:text-3xl text-green-900"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">Clinical Training</h3>
                <p class="text-gray-700 leading-relaxed text-sm md:text-base">
                    Hands-on training in modern healthcare facilities and partnerships with leading hospitals for practical experience.
                </p>
            </div>
            
            <div class="bg-white border-2 border-gray-100 p-6 md:p-8 rounded-lg shadow-md hover:shadow-xl transition text-center">
                <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-microscope text-2xl md:text-3xl text-green-900"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">Modern Labs</h3>
                <p class="text-gray-700 leading-relaxed text-sm md:text-base">
                    State-of-the-art laboratories equipped with latest medical equipment and technology for practical learning.
                </p>
            </div>
            
            <div class="bg-white border-2 border-gray-100 p-6 md:p-8 rounded-lg shadow-md hover:shadow-xl transition text-center">
                <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-certificate text-2xl md:text-3xl text-green-900"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">Recognized Programs</h3>
                <p class="text-gray-700 leading-relaxed text-sm md:text-base">
                    Approved courses and certifications ensuring your qualification is recognized by healthcare institutions nationwide.
                </p>
            </div>
            
            <div class="bg-white border-2 border-gray-100 p-6 md:p-8 rounded-lg shadow-md hover:shadow-xl transition text-center">
                <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-heartbeat text-2xl md:text-3xl text-green-900"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">Career Support</h3>
                <p class="text-gray-700 leading-relaxed text-sm md:text-base">
                    Placement assistance and career guidance helping you secure positions in hospitals, clinics, and healthcare organizations.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Popular Courses Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Popular Courses</h2>
            <div class="w-24 h-1 bg-green-600 mx-auto mb-6"></div>
            <p class="text-gray-700 text-lg max-w-3xl mx-auto font-medium">
                Discover our healthcare and paramedical programs designed for your professional growth
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @php
                $sampleCourses = [
                    ['name' => 'DMLT', 'full' => 'Diploma in Medical Laboratory Technology', 'duration' => '2 Years', 'icon' => 'fa-flask', 'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=600&h=400&fit=crop', 'description' => 'Learn laboratory techniques and diagnostic procedures'],
                    ['name' => 'B.Sc Nursing', 'full' => 'Bachelor of Science in Nursing', 'duration' => '4 Years', 'icon' => 'fa-heartbeat', 'image' => 'https://images.unsplash.com/photo-1559839734876-b7833e8f5570?w=600&h=400&fit=crop', 'description' => 'Comprehensive nursing program for healthcare excellence'],
                    ['name' => 'Pharmacy', 'full' => 'Diploma in Pharmacy', 'duration' => '2 Years', 'icon' => 'fa-pills', 'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=600&h=400&fit=crop', 'description' => 'Master pharmaceutical sciences and patient care'],
                ];
            @endphp
            @foreach($sampleCourses as $course)
            <div class="bg-white border-2 border-gray-200 rounded-lg shadow-md hover:shadow-xl transition group overflow-hidden">
                <img src="{{ $course['image'] }}" alt="{{ $course['name'] }}" class="w-full h-48 object-cover">
                <div class="p-6">
                    <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:bg-green-900 transition">
                        <i class="fas {{ $course['icon'] }} text-xl text-green-900 group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $course['name'] }}</h3>
                    <p class="text-gray-700 font-semibold mb-2">{{ $course['full'] }}</p>
                    <p class="text-gray-600 text-sm mb-4">{{ $course['description'] }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-green-900 font-semibold text-sm">
                            <i class="fas fa-clock mr-2"></i>{{ $course['duration'] }}
                        </span>
                        <a href="{{ route('courses') }}" class="text-green-600 hover:text-green-800 font-medium text-sm">
                            Learn More <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            <a href="{{ route('courses') }}" class="bg-green-900 text-white px-8 py-4 rounded-lg font-semibold hover:bg-green-800 transition shadow-lg text-lg inline-block">
                <i class="fas fa-list mr-2"></i>View All Courses
            </a>
        </div>
    </div>
</section>

<!-- Facilities Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Facilities</h2>
            <div class="w-24 h-1 bg-green-600 mx-auto mb-6"></div>
            <p class="text-gray-700 text-lg max-w-3xl mx-auto font-medium">
                World-class infrastructure supporting healthcare education and training
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white border-2 border-gray-100 rounded-lg shadow-md text-center hover:shadow-xl transition overflow-hidden">
                <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&h=300&fit=crop" alt="Medical Laboratory" class="w-full h-40 object-cover">
                <div class="p-6">
                    <i class="fas fa-microscope text-3xl md:text-4xl text-green-900 mb-4"></i>
                    <h4 class="font-bold text-gray-900 mb-2 text-lg">Laboratory</h4>
                    <p class="text-gray-700 text-sm">Modern medical labs with advanced equipment</p>
                </div>
            </div>
            <div class="bg-white border-2 border-gray-100 rounded-lg shadow-md text-center hover:shadow-xl transition overflow-hidden">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=400&h=300&fit=crop" alt="Hospital" class="w-full h-40 object-cover">
                <div class="p-6">
                    <i class="fas fa-hospital text-3xl md:text-4xl text-green-900 mb-4"></i>
                    <h4 class="font-bold text-gray-900 mb-2 text-lg">Clinical Training</h4>
                    <p class="text-gray-700 text-sm">Partnerships with hospitals for practical training</p>
                </div>
            </div>
            <div class="bg-white border-2 border-gray-100 rounded-lg shadow-md text-center hover:shadow-xl transition overflow-hidden">
                <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=300&fit=crop" alt="Library" class="w-full h-40 object-cover">
                <div class="p-6">
                    <i class="fas fa-book-medical text-3xl md:text-4xl text-green-900 mb-4"></i>
                    <h4 class="font-bold text-gray-900 mb-2 text-lg">Library</h4>
                    <p class="text-gray-700 text-sm">Extensive medical and healthcare resources</p>
                </div>
            </div>
            <div class="bg-white border-2 border-gray-100 rounded-lg shadow-md text-center hover:shadow-xl transition overflow-hidden">
                <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=400&h=300&fit=crop" alt="Smart Classrooms" class="w-full h-40 object-cover">
                <div class="p-6">
                    <i class="fas fa-laptop-medical text-3xl md:text-4xl text-green-900 mb-4"></i>
                    <h4 class="font-bold text-gray-900 mb-2 text-lg">Smart Classrooms</h4>
                    <p class="text-gray-700 text-sm">Digital learning environment with multimedia</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Clinical Training Section -->
<section class="py-16 bg-gradient-to-r from-green-900 to-green-800 text-white relative overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=1920&h=1080&fit=crop" alt="Medical training" class="w-full h-full object-cover opacity-10">
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Clinical Training & Placement</h2>
            <div class="w-24 h-1 bg-white mx-auto mb-6"></div>
            <p class="text-green-100 text-lg max-w-3xl mx-auto font-medium">
                Real-world experience through hospital partnerships and placement support
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-6 md:p-8 text-center border border-white border-opacity-20">
                <div class="bg-green-800 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-hospital text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold mb-4">Hospital Partnerships</h3>
                <p class="text-green-100 text-sm md:text-base">Tie-ups with leading hospitals and healthcare centers for clinical training and internships</p>
            </div>
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-6 md:p-8 text-center border border-white border-opacity-20">
                <div class="bg-green-800 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user-nurse text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold mb-4">Expert Guidance</h3>
                <p class="text-green-100 text-sm md:text-base">Mentorship from experienced healthcare professionals ensuring practical skill development</p>
            </div>
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-6 md:p-8 text-center border border-white border-opacity-20">
                <div class="bg-green-800 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-briefcase-medical text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold mb-4">Career Opportunities</h3>
                <p class="text-green-100 text-sm md:text-base">Placement assistance connecting graduates with hospitals, clinics, and diagnostic centers</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-16 bg-gradient-to-r from-green-800 to-green-900 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-6">Start Your Healthcare Career Today</h2>
        <p class="text-lg md:text-xl text-green-100 mb-8 font-medium">
            Join us and become part of a community dedicated to excellence in healthcare education and service
        </p>
        <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('student.login') }}" class="bg-white text-green-900 px-8 py-4 rounded-lg font-semibold hover:bg-green-50 transition shadow-xl text-lg w-full sm:w-auto">
                <i class="fas fa-user-graduate mr-2"></i>Student Portal
            </a>
            <a href="{{ route('courses') }}" class="bg-green-700 text-white px-8 py-4 rounded-lg font-semibold hover:bg-green-600 transition shadow-xl text-lg border-2 border-green-500 w-full sm:w-auto">
                <i class="fas fa-list mr-2"></i>View Courses
            </a>
        </div>
    </div>
</section>
@endsection
