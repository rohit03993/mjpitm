<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Student Dashboard - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg border-b-4 border-amber-500">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
                <div class="flex justify-between items-center h-14 sm:h-16">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="flex-shrink-0 flex items-center space-x-2 sm:space-x-3">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h1 class="text-base sm:text-xl font-bold text-gray-900 truncate">
                                Student Portal
                            </h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-4 flex-shrink-0">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-900 truncate max-w-[120px]">{{ $student->name }}</p>
                            <p class="text-xs text-gray-500 truncate max-w-[120px]">{{ $student->registration_number ?? '—' }}</p>
                        </div>
                        <form method="POST" action="{{ route('student.logout') }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <span class="hidden sm:inline">Logout</span>
                                <span class="sm:hidden">Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-4 sm:py-6 px-3 sm:px-6 lg:px-8">
            <div class="py-4 sm:py-6">
                <!-- Welcome Message with Institute Branding -->
                @php
                    $isParamedical = $student->institute && \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($student->institute->name), 'paramedical');
                    $gradientClass = $isParamedical ? 'from-emerald-600 to-green-500' : 'from-blue-700 to-indigo-600';
                    $logoFile = $isParamedical ? 'MJPIPS.png' : 'MJPITM.png';
                @endphp
                <div class="bg-gradient-to-r {{ $gradientClass }} overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-8 text-white">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Student Photo or Icon -->
                                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-xl flex items-center justify-center overflow-hidden border-3 border-white border-opacity-30">
                                    @if($student->photo)
                                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Photo" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold mb-1">Welcome, {{ $student->name }}!</h2>
                                    <p class="text-sm text-white text-opacity-90 mb-3">{{ $student->institute->name ?? 'Your Institute' }}</p>
                                    <div class="flex flex-wrap gap-3 text-sm">
                                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full">
                                            <strong>Reg:</strong> {{ $student->registration_number ?? '—' }}
                                        </span>
                                        @if($student->roll_number)
                                        <span class="bg-yellow-400 bg-opacity-30 px-3 py-1 rounded-full">
                                            <strong>Roll:</strong> {{ $student->roll_number }}
                                        </span>
                                        @endif
                                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full {{ $student->status === 'active' ? 'bg-green-400' : 'bg-yellow-400' }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Institute Logo -->
                            <div class="hidden md:block w-20 h-20 bg-white rounded-xl p-2 shadow-lg">
                                <img src="{{ asset('images/logos/' . $logoFile) }}" alt="Institute Logo" class="w-full h-full object-contain">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Information Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Course Information -->
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-blue-500">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Course Information</h3>
                            </div>
                            <div class="space-y-2">
                                <p class="text-gray-700">
                                    <span class="font-medium">Course:</span> 
                                    <span class="text-gray-900">{{ $student->course->name ?? 'N/A' }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Duration:</span> 
                                    <span class="text-gray-900">{{ $student->course->formatted_duration ?? 'N/A' }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Semester:</span> 
                                    <span class="text-gray-900">{{ $student->current_semester ?? '1' }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Session:</span> 
                                    <span class="text-gray-900">{{ $student->session ?? ($student->admission_year . '-' . ($student->admission_year + 1)) }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Mode:</span> 
                                    <span class="text-gray-900">{{ ucfirst($student->mode_of_study ?? 'Regular') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Results Status -->
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-purple-500">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Results Status</h3>
                            </div>
                            <div class="space-y-2">
                                <p class="text-gray-700">
                                    <span class="font-medium">Total Results:</span> 
                                    <span class="text-gray-900">{{ $student->results->count() }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Published:</span> 
                                    <span class="text-green-600 font-semibold">{{ $student->results->where('status', 'published')->count() }}</span>
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Pending:</span> 
                                    <span class="text-yellow-600 font-semibold">{{ $student->results->where('status', 'pending_verification')->count() }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900">Personal Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <p class="text-sm text-gray-500">Father's Name</p>
                                <p class="font-medium text-gray-900">{{ $student->father_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mother's Name</p>
                                <p class="font-medium text-gray-900">{{ $student->mother_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Date of Birth</p>
                                <p class="font-medium text-gray-900">{{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Gender</p>
                                <p class="font-medium text-gray-900">{{ ucfirst($student->gender ?? 'N/A') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium text-gray-900">{{ $student->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium text-gray-900">{{ $student->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Category</p>
                                <p class="font-medium text-gray-900">{{ strtoupper($student->category ?? 'N/A') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Address</p>
                                <p class="font-medium text-gray-900">{{ $student->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Certificates Section -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900">My Academic Certificates</h3>
                        <p class="text-sm text-gray-500 mt-1">View and download your uploaded certificates</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- Class 10th Certificate --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:shadow-md transition">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Class 10th Certificate</h4>
                                @if($student->certificate_class_10th)
                                    @php
                                        $isPdf = pathinfo($student->certificate_class_10th, PATHINFO_EXTENSION) === 'pdf';
                                    @endphp
                                    @if($isPdf)
                                        <a href="{{ asset('storage/' . $student->certificate_class_10th) }}" target="_blank" class="block">
                                            <div class="w-full h-32 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-50 transition">
                                                <div class="text-center">
                                                    <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-xs text-gray-600">PDF Document</p>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $student->certificate_class_10th) }}" target="_blank" class="block">
                                            <img src="{{ asset('storage/' . $student->certificate_class_10th) }}" alt="Class 10th Certificate" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $student->certificate_class_10th) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline block">View / Download</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Class 12th Certificate --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:shadow-md transition">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Class 12th Certificate</h4>
                                @if($student->certificate_class_12th)
                                    @php
                                        $isPdf = pathinfo($student->certificate_class_12th, PATHINFO_EXTENSION) === 'pdf';
                                    @endphp
                                    @if($isPdf)
                                        <a href="{{ asset('storage/' . $student->certificate_class_12th) }}" target="_blank" class="block">
                                            <div class="w-full h-32 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-50 transition">
                                                <div class="text-center">
                                                    <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-xs text-gray-600">PDF Document</p>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $student->certificate_class_12th) }}" target="_blank" class="block">
                                            <img src="{{ asset('storage/' . $student->certificate_class_12th) }}" alt="Class 12th Certificate" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $student->certificate_class_12th) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline block">View / Download</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Graduation Certificate --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:shadow-md transition">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Graduation Certificate</h4>
                                @if($student->certificate_graduation)
                                    @php
                                        $isPdf = pathinfo($student->certificate_graduation, PATHINFO_EXTENSION) === 'pdf';
                                    @endphp
                                    @if($isPdf)
                                        <a href="{{ asset('storage/' . $student->certificate_graduation) }}" target="_blank" class="block">
                                            <div class="w-full h-32 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-50 transition">
                                                <div class="text-center">
                                                    <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-xs text-gray-600">PDF Document</p>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $student->certificate_graduation) }}" target="_blank" class="block">
                                            <img src="{{ asset('storage/' . $student->certificate_graduation) }}" alt="Graduation Certificate" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $student->certificate_graduation) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline block">View / Download</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Other Certificates --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:shadow-md transition">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Other Certificates</h4>
                                @if($student->certificate_others)
                                    @php
                                        $isPdf = pathinfo($student->certificate_others, PATHINFO_EXTENSION) === 'pdf';
                                    @endphp
                                    @if($isPdf)
                                        <a href="{{ asset('storage/' . $student->certificate_others) }}" target="_blank" class="block">
                                            <div class="w-full h-32 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-50 transition">
                                                <div class="text-center">
                                                    <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-xs text-gray-600">PDF Document</p>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $student->certificate_others) }}" target="_blank" class="block">
                                            <img src="{{ asset('storage/' . $student->certificate_others) }}" alt="Other Certificates" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $student->certificate_others) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline block">View / Download</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Published Results Section -->
                <div id="results-section" class="bg-white overflow-hidden shadow-lg sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-semibold text-gray-900">Published Results</h3>
                            <span class="text-sm text-gray-500">{{ $publishedResults->count() }} result(s) published</span>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($publishedResults->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($publishedResults as $result)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $result->subject->name ?? 'N/A' }}</div>
                                                    <div class="text-xs text-gray-500">{{ $result->subject->code ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ ucfirst($result->exam_type ?? 'N/A') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    Sem {{ $result->semester }} ({{ $result->academic_year }})
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <span class="font-semibold">{{ $result->marks_obtained ?? 'N/A' }}</span>
                                                    <span class="text-gray-500">/ {{ $result->total_marks ?? 'N/A' }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                                    {{ $result->percentage ?? 'N/A' }}%
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ $result->grade ?? 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-8">No published results available yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="#results" onclick="document.getElementById('results-section').scrollIntoView({behavior: 'smooth'}); return false;" class="block p-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg hover:shadow-md transition border border-indigo-100">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-indigo-900 text-lg">View Results</h4>
                                        <p class="text-sm text-indigo-700 mt-1">Check your exam results</p>
                                    </div>
                                </div>
                            </a>
                            @if($student->status === 'active' && $student->roll_number)
                            <a href="{{ route('student.documents.view.idcard') }}" class="block p-6 bg-gradient-to-br from-teal-50 to-cyan-50 rounded-lg hover:shadow-md transition border border-teal-200">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-teal-500 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-teal-900 text-lg">View ID Card</h4>
                                        <p class="text-sm text-teal-700 mt-1">Preview & download your ID card</p>
                                    </div>
                                </div>
                            </a>
                            @else
                            <div class="block p-6 bg-gradient-to-br from-gray-50 to-slate-50 rounded-lg border border-gray-200 opacity-60">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gray-400 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-600 text-lg">ID Card</h4>
                                        <p class="text-sm text-gray-500 mt-1">Available after approval</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <a href="{{ route('home') }}" class="block p-6 bg-gradient-to-br from-gray-50 to-slate-50 rounded-lg hover:shadow-md transition border border-gray-200">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gray-500 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-lg">Institute Website</h4>
                                        <p class="text-sm text-gray-700 mt-1">Visit the institute website</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

