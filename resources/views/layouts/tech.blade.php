<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Mahatma Jyotiba Phule Institute of Technology & Management - Excellence in Technical Education & Management Studies. Offering BCA, BBA, MCA and more programs.">

    <title>{{ $institute->name ?? 'Mahatma Jyotiba Phule Institute of Technology & Management' }} | Excellence in Technical Education</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=playfair+display:400,500,600,700&family=merriweather:400,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-900 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center min-h-[70px] md:min-h-[80px] xl:min-h-[90px] py-2">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 md:space-x-3 lg:space-x-4 hover:opacity-95 transition-opacity duration-200">
                        @if(file_exists(public_path('images/logos/MJPITM.png')) || file_exists(public_path('images/logos/MJPITM.jpg')) || file_exists(public_path('images/logos/MJPITM.svg')))
                            @php
                                $logoPath = file_exists(public_path('images/logos/MJPITM.png')) ? 'images/logos/MJPITM.png' : (file_exists(public_path('images/logos/MJPITM.jpg')) ? 'images/logos/MJPITM.jpg' : 'images/logos/MJPITM.svg');
                            @endphp
                            <div class="flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg md:rounded-xl p-1.5 md:p-2 lg:p-2.5 border-2 border-blue-300 shadow-md md:shadow-lg hover:shadow-xl hover:border-blue-400 transition-all duration-200">
                                <img src="{{ asset($logoPath) }}" alt="MJPITM Logo" class="h-10 sm:h-12 md:h-16 lg:h-20 xl:h-24 w-auto max-w-[120px] sm:max-w-[140px] md:max-w-[180px] lg:max-w-[220px] xl:max-w-[260px] object-contain drop-shadow-sm md:drop-shadow-md">
                            </div>
                            <div class="hidden lg:block ml-3 max-w-lg">
                                <div class="text-white font-bold text-base lg:text-lg xl:text-xl leading-tight" style="font-family: 'Playfair Display', 'Merriweather', 'Georgia', serif; line-height: 1.4;">
                                    <div class="whitespace-nowrap">Mahatma Jyotiba Phule</div>
                                    <div class="whitespace-nowrap">Institute of Technology & Management</div>
                                </div>
                            </div>
                        @else
                            <div class="bg-white rounded-full p-2">
                                <i class="fas fa-graduation-cap text-blue-900 text-2xl"></i>
                            </div>
                            <div>
                                <div class="text-lg font-bold">MJPITM</div>
                                <div class="text-xs text-blue-200">Excellence in Education</div>
                            </div>
                        @endif
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="hover:text-blue-200 px-3 py-2 rounded-md transition {{ request()->routeIs('home') ? 'text-blue-200 border-b-2 border-blue-200' : '' }}">Home</a>
                    <a href="{{ route('about') }}" class="hover:text-blue-200 px-3 py-2 rounded-md transition {{ request()->routeIs('about') ? 'text-blue-200 border-b-2 border-blue-200' : '' }}">About</a>
                    <a href="{{ route('courses') }}" class="hover:text-blue-200 px-3 py-2 rounded-md transition {{ request()->routeIs('courses') ? 'text-blue-200 border-b-2 border-blue-200' : '' }}">Courses</a>
                    <a href="{{ route('login') }}" class="bg-white text-blue-900 hover:bg-blue-50 px-4 py-2 rounded-md font-semibold transition shadow-md hover:shadow-lg">
                        <i class="fas fa-user-tie mr-2"></i>Staff Login
                    </a>
                    <a href="{{ route('student.login') }}" class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded-md font-semibold transition shadow-md hover:shadow-lg">
                        <i class="fas fa-user-graduate mr-2"></i>Student Portal
                    </a>
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-white hover:text-blue-200 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-blue-800 pb-4">
            <div class="px-4 space-y-2">
                <a href="{{ route('home') }}" class="block hover:text-blue-200 px-3 py-2 rounded-md">Home</a>
                <a href="{{ route('about') }}" class="block hover:text-blue-200 px-3 py-2 rounded-md">About</a>
                <a href="{{ route('courses') }}" class="block hover:text-blue-200 px-3 py-2 rounded-md">Courses</a>
                <a href="{{ route('login') }}" class="block bg-white text-blue-900 hover:bg-blue-50 px-3 py-2 rounded-md font-semibold text-center mb-2">
                    <i class="fas fa-user-tie mr-2"></i>Staff Login
                </a>
                <a href="{{ route('student.login') }}" class="block bg-blue-700 hover:bg-blue-800 px-3 py-2 rounded-md font-semibold text-center">
                    <i class="fas fa-user-graduate mr-2"></i>Student Portal
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="mb-4">
                        @if(file_exists(public_path('images/logos/MJPITM.png')) || file_exists(public_path('images/logos/MJPITM.jpg')) || file_exists(public_path('images/logos/MJPITM.svg')))
                            @php
                                $logoPath = file_exists(public_path('images/logos/MJPITM.png')) ? 'images/logos/MJPITM.png' : (file_exists(public_path('images/logos/MJPITM.jpg')) ? 'images/logos/MJPITM.jpg' : 'images/logos/MJPITM.svg');
                            @endphp
                            <div class="inline-block bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-3 border-2 border-blue-300 shadow-md mb-3">
                                <img src="{{ asset($logoPath) }}" alt="MJPITM Logo" class="h-16 md:h-20 lg:h-24 w-auto max-w-[240px] md:max-w-[280px] lg:max-w-[300px] object-contain drop-shadow-sm">
                            </div>
                        @else
                            <h3 class="text-xl font-bold mb-4 flex items-center">
                                <i class="fas fa-graduation-cap mr-2 text-blue-400"></i>
                                MJPITM
                            </h3>
                        @endif
                    </div>
                    <p class="text-gray-400 mb-4">
                        {{ $institute->description ?? 'Mahatma Jyotiba Phule Institute of Technology & Management - A premier institution for technical and management education.' }}
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>Home</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>About Us</a></li>
                        <li><a href="{{ route('courses') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>Courses</a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>Staff Login</a></li>
                        <li><a href="{{ route('student.login') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>Student Portal</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Programs</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('courses') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>BCA</a></li>
                        <li><a href="{{ route('courses') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>BBA</a></li>
                        <li><a href="{{ route('courses') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>MCA</a></li>
                        <li><a href="{{ route('courses') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs"></i>All Courses</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Contact Us</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mr-3 text-blue-400 mt-1"></i>
                            <span class="text-gray-400">Agra, Uttar Pradesh, India</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-blue-400"></i>
                            <a href="mailto:info@mjfitm.in" class="text-gray-400 hover:text-white transition">info@mjfitm.in</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-blue-400"></i>
                            <a href="tel:+91XXXXXXXXXX" class="text-gray-400 hover:text-white transition">+91-XXXXX-XXXXX</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-globe mr-3 text-blue-400"></i>
                            <a href="http://mjfitm.in" class="text-gray-400 hover:text-white transition">www.mjfitm.in</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">
                    &copy; {{ date('Y') }} {{ $institute->name ?? 'Mahatma Jyotiba Phule Institute of Technology & Management' }}. All rights reserved.
                </p>
                <p class="text-gray-500 text-sm mt-2">Designed with <i class="fas fa-heart text-red-500"></i> for Excellence in Education</p>
            </div>
        </div>
    </footer>

    <!-- Mobile menu script -->
    <script>
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
