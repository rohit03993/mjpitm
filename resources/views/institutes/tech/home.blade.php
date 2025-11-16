@extends('layouts.tech')

@section('content')
<!-- Hero Section with Modern Design -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900">
    <!-- Animated Background -->
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1920&h=1080&fit=crop" alt="Students learning" class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 via-blue-800/80 to-indigo-900/90"></div>
    </div>
    
    <!-- Animated Shapes -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-10 w-72 h-72 bg-blue-400/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-indigo-400/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-center lg:text-left space-y-8 animate-fade-in">
                <div class="space-y-4">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight">
                        {{ $institute->name ?? 'Mahatma Jyotiba Phule Institute of Technology & Management' }}
                    </h1>
                    <div class="h-1.5 w-24 bg-gradient-to-r from-blue-400 to-indigo-400 mx-auto lg:mx-0 rounded-full"></div>
                </div>
                
                <p class="text-xl md:text-2xl font-semibold text-blue-100 leading-relaxed">
                    Excellence in Technical Education & Management Studies
                </p>
                
                <p class="text-lg text-blue-200 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    Empowering the next generation of technology leaders and business professionals through quality education, industry exposure, and holistic development.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start items-center gap-4 pt-4">
                    <a href="{{ route('courses') }}" class="group relative px-8 py-4 bg-white text-blue-900 rounded-xl font-bold text-lg shadow-2xl hover:shadow-blue-500/50 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                        <span class="relative z-10 flex items-center">
                            <i class="fas fa-rocket mr-2"></i>Explore Courses
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </a>
                    <a href="{{ route('about') }}" class="px-8 py-4 bg-transparent border-3 border-white text-white rounded-xl font-bold text-lg hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-1 backdrop-blur-sm">
                        <i class="fas fa-info-circle mr-2"></i>Learn More
                    </a>
                </div>
            </div>
            
            <!-- Right Image -->
            <div class="hidden lg:block animate-slide-in-right">
                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-400 to-indigo-400 rounded-2xl blur-2xl opacity-50"></div>
                    <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&h=600&fit=crop" alt="Modern computer lab" class="relative rounded-2xl shadow-2xl transform hover:scale-105 transition-transform duration-500">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Wave Divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 100C120 80 240 40 360 30C480 20 600 40 720 50C840 60 960 60 1080 50C1200 40 1320 20 1380 10L1440 0V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- Statistics Section - Modern Cards -->
<section class="py-20 bg-gradient-to-br from-white via-blue-50 to-white -mt-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="group bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-blue-100 hover:border-blue-300 transform hover:-translate-y-2">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-users text-3xl text-white"></i>
                </div>
                <h4 class="text-4xl font-extrabold text-gray-900 mb-2 text-center">1000+</h4>
                <p class="text-gray-600 font-semibold text-center">Students</p>
            </div>
            <div class="group bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-blue-100 hover:border-blue-300 transform hover:-translate-y-2">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-graduation-cap text-3xl text-white"></i>
                </div>
                <h4 class="text-4xl font-extrabold text-gray-900 mb-2 text-center">10+</h4>
                <p class="text-gray-600 font-semibold text-center">Courses</p>
            </div>
            <div class="group bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-blue-100 hover:border-blue-300 transform hover:-translate-y-2">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-chalkboard-teacher text-3xl text-white"></i>
                </div>
                <h4 class="text-4xl font-extrabold text-gray-900 mb-2 text-center">50+</h4>
                <p class="text-gray-600 font-semibold text-center">Faculty</p>
            </div>
            <div class="group bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-blue-100 hover:border-blue-300 transform hover:-translate-y-2">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-building text-3xl text-white"></i>
                </div>
                <h4 class="text-4xl font-extrabold text-gray-900 mb-2 text-center">15+</h4>
                <p class="text-gray-600 font-semibold text-center">Years</p>
            </div>
        </div>
        </div>
    </div>
</section>

<!-- About Section - Modern Design -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-bold mb-4">ABOUT US</span>
            <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">About Our Institute</h2>
            <div class="h-1.5 w-24 bg-gradient-to-r from-blue-500 to-indigo-600 mx-auto rounded-full"></div>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mt-6 font-medium">
                A premier institution dedicated to providing excellence in technical and management education
            </p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative group">
                <div class="absolute -inset-4 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-3xl blur-xl opacity-30 group-hover:opacity-50 transition-opacity"></div>
                <img src="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=800&h=600&fit=crop" alt="Campus view" class="relative rounded-3xl shadow-2xl transform group-hover:scale-105 transition-transform duration-500">
            </div>
            
            <div class="space-y-8">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl border-l-4 border-blue-600">
                    <h3 class="text-3xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-bullseye text-blue-600 mr-3"></i>Our Mission
                    </h3>
                    <p class="text-gray-700 leading-relaxed text-lg">
                        To provide quality technical and management education that prepares students for successful careers in the industry. We are committed to nurturing talent, fostering innovation, and building character through comprehensive academic programs and practical training.
                    </p>
                </div>
                
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-8 rounded-2xl border-l-4 border-indigo-600">
                    <h3 class="text-3xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-eye text-indigo-600 mr-3"></i>Our Vision
                    </h3>
                    <p class="text-gray-700 leading-relaxed text-lg">
                        To be a leading institute in technical and management education, recognized for excellence in teaching, research, and industry collaboration. We aim to produce graduates who are not only technically competent but also socially responsible leaders.
                    </p>
                </div>
                
                <a href="{{ route('about') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    Read More <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us - Modern Cards -->
<section class="py-20 bg-gradient-to-br from-gray-50 via-blue-50 to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-bold mb-4">WHY CHOOSE US</span>
            <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">Why Choose Us?</h2>
            <div class="h-1.5 w-24 bg-gradient-to-r from-blue-500 to-indigo-600 mx-auto rounded-full"></div>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mt-6 font-medium">
                Committed to providing quality technical education with industry-aligned curriculum
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $features = [
                    ['icon' => 'fa-book-reader', 'title' => 'Quality Education', 'desc' => 'Comprehensive curriculum designed for modern industry needs with emphasis on practical learning and hands-on experience.'],
                    ['icon' => 'fa-briefcase', 'title' => 'Career Opportunities', 'desc' => 'Strong placement support and industry connections ensuring high employability with dedicated placement cell.'],
                    ['icon' => 'fa-building', 'title' => 'Modern Facilities', 'desc' => 'State-of-the-art labs, library, and infrastructure providing the best learning environment for students.'],
                    ['icon' => 'fa-user-tie', 'title' => 'Expert Faculty', 'desc' => 'Experienced professors and industry experts committed to student success and academic excellence.'],
                    ['icon' => 'fa-laptop-code', 'title' => 'Industry Partnerships', 'desc' => 'Collaborations with leading companies for internships, projects, and placement opportunities.'],
                    ['icon' => 'fa-certificate', 'title' => 'Recognition & Accreditation', 'desc' => 'Recognized programs and affiliations ensuring quality standards and value of your degree.'],
                ];
            @endphp
            @foreach($features as $feature)
            <div class="group bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-blue-300 transform hover:-translate-y-2">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <i class="fas {{ $feature['icon'] }} text-2xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $feature['title'] }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Popular Courses - Modern Cards with Images -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-bold mb-4">OUR COURSES</span>
            <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">Popular Courses</h2>
            <div class="h-1.5 w-24 bg-gradient-to-r from-blue-500 to-indigo-600 mx-auto rounded-full"></div>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mt-6 font-medium">
                Explore our range of technical and management programs designed for your career success
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $courses = [
                    ['name' => 'BCA', 'full' => 'Bachelor of Computer Applications', 'duration' => '3 Years', 'icon' => 'fa-code', 'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=400&fit=crop'],
                    ['name' => 'BBA', 'full' => 'Bachelor of Business Administration', 'duration' => '3 Years', 'icon' => 'fa-briefcase', 'image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=600&h=400&fit=crop'],
                    ['name' => 'MCA', 'full' => 'Master of Computer Applications', 'duration' => '2 Years', 'icon' => 'fa-laptop-code', 'image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=600&h=400&fit=crop'],
                ];
            @endphp
            @foreach($courses as $course)
            <div class="group bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-300 transform hover:-translate-y-2">
                <div class="relative overflow-hidden">
                    <img src="{{ $course['image'] }}" alt="{{ $course['name'] }}" class="w-full h-56 object-cover transform group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-900/80 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <div class="bg-white/90 backdrop-blur-sm px-4 py-2 rounded-lg">
                            <i class="fas {{ $course['icon'] }} text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $course['name'] }}</h3>
                    <p class="text-gray-700 font-semibold mb-3">{{ $course['full'] }}</p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <span class="flex items-center text-blue-600 font-bold">
                            <i class="fas fa-clock mr-2"></i>{{ $course['duration'] }}
                        </span>
                        <a href="{{ route('courses') }}" class="text-blue-600 hover:text-blue-800 font-bold flex items-center group-hover:translate-x-2 transition-transform">
                            Learn More <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            <a href="{{ route('courses') }}" class="inline-flex items-center px-10 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                <i class="fas fa-list mr-3"></i>View All Courses
            </a>
        </div>
    </div>
</section>

<!-- Facilities Section -->
<section class="py-20 bg-gradient-to-br from-blue-50 via-white to-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-bold mb-4">FACILITIES</span>
            <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">Our Facilities</h2>
            <div class="h-1.5 w-24 bg-gradient-to-r from-blue-500 to-indigo-600 mx-auto rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $facilities = [
                    ['icon' => 'fa-flask', 'title' => 'Computer Labs', 'desc' => 'Well-equipped labs with latest hardware', 'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=300&fit=crop'],
                    ['icon' => 'fa-book', 'title' => 'Library', 'desc' => 'Extensive collection of books', 'image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=300&fit=crop'],
                    ['icon' => 'fa-wifi', 'title' => 'Wi-Fi Campus', 'desc' => 'High-speed internet connectivity', 'image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=400&h=300&fit=crop'],
                    ['icon' => 'fa-chalkboard', 'title' => 'Smart Classrooms', 'desc' => 'Modern multimedia facilities', 'image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=400&h=300&fit=crop'],
                ];
            @endphp
            @foreach($facilities as $facility)
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-300 transform hover:-translate-y-2">
                <div class="relative h-40 overflow-hidden">
                    <img src="{{ $facility['image'] }}" alt="{{ $facility['title'] }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-900/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <div class="bg-white/90 backdrop-blur-sm w-12 h-12 rounded-xl flex items-center justify-center">
                            <i class="fas {{ $facility['icon'] }} text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $facility['title'] }}</h4>
                    <p class="text-gray-600 text-sm">{{ $facility['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Placement Section - Modern Gradient -->
<section class="py-20 bg-gradient-to-br from-blue-900 via-indigo-900 to-blue-900 text-white relative overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1920&h=1080&fit=crop" alt="Business" class="w-full h-full object-cover opacity-10">
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold mb-4">PLACEMENT & CAREER</span>
            <h2 class="text-4xl md:text-5xl font-extrabold mb-6">Placement & Career Support</h2>
            <div class="h-1.5 w-24 bg-white mx-auto rounded-full"></div>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto mt-6 font-medium">
                We ensure our students are well-prepared for their professional journey
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $placementFeatures = [
                    ['icon' => 'fa-handshake', 'title' => 'Industry Connect', 'desc' => 'Strong partnerships with leading companies for internships and placements'],
                    ['icon' => 'fa-user-graduate', 'title' => 'Career Guidance', 'desc' => 'Dedicated placement cell providing career counseling and skill development'],
                    ['icon' => 'fa-chart-line', 'title' => 'High Placement Rate', 'desc' => 'Consistent track record of excellent placement opportunities'],
                ];
            @endphp
            @foreach($placementFeatures as $feature)
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-2">
                <div class="bg-white/20 backdrop-blur-sm w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas {{ $feature['icon'] }} text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-center">{{ $feature['title'] }}</h3>
                <p class="text-blue-100 text-center leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Call to Action - Modern Design -->
<section class="py-20 bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-700 text-white relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-400/20 rounded-full blur-3xl"></div>
    </div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-extrabold mb-6">Ready to Start Your Journey?</h2>
        <p class="text-xl text-blue-100 mb-10 font-medium leading-relaxed">
            Join us and become part of a community dedicated to excellence in technical and management education
        </p>
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
            <a href="{{ route('student.login') }}" class="group px-10 py-5 bg-white text-blue-900 rounded-xl font-bold text-lg shadow-2xl hover:shadow-white/50 transition-all duration-300 transform hover:-translate-y-2 hover:scale-105">
                <i class="fas fa-user-graduate mr-2"></i>Student Portal
            </a>
            <a href="{{ route('courses') }}" class="px-10 py-5 bg-transparent border-3 border-white text-white rounded-xl font-bold text-lg hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-2 backdrop-blur-sm">
                <i class="fas fa-list mr-2"></i>View Courses
            </a>
        </div>
    </div>
</section>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-in-right {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.animate-slide-in-right {
    animation: slide-in-right 0.8s ease-out;
}

.delay-1000 {
    animation-delay: 1s;
}
</style>
@endsection
