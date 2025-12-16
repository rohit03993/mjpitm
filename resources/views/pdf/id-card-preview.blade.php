<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ID Card Preview - {{ $student->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .id-card-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 50vh;
        }
        
        .id-card {
            width: 450px;
            height: 280px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            padding: 16px;
        }
        
        .id-card-tech {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
        
        .id-card-para {
            background: linear-gradient(135deg, #065f46 0%, #10b981 100%);
        }
        
        .watermark-logo {
            position: absolute;
            top: 50%;
            right: 30px;
            transform: translateY(-50%);
            width: 220px;
            height: 220px;
            opacity: 0.12;
            pointer-events: none;
            z-index: 0;
        }
        
        .watermark-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .card-content {
            position: relative;
            z-index: 1;
            height: 100%;
        }
        
        .card-header {
            background: rgba(255, 255, 255, 0.95);
            padding: 10px 16px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        
        .institute-name {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }
        
        .institute-name-tech {
            color: #1e3a8a;
        }
        
        .institute-name-para {
            color: #065f46;
        }
        
        .card-title {
            font-size: 14px;
            font-weight: 700;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 6px;
        }
        
        .card-body {
            display: flex;
            gap: 16px;
        }
        
        .photo-box {
            width: 90px;
            height: 110px;
            background: #ffffff;
            border: 3px solid #ffffff;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #9ca3af;
            font-size: 12px;
        }
        
        .info-section {
            flex: 1;
        }
        
        .name-roll-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .student-name {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            text-transform: uppercase;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .info-row {
            margin-bottom: 3px;
        }
        
        .info-label {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            display: inline;
        }
        
        .info-value {
            font-size: 13px;
            color: #ffffff;
            font-weight: 600;
            display: inline;
        }
        
        .roll-box {
            background: rgba(255, 255, 255, 0.25);
            padding: 5px 10px;
            border-radius: 6px;
            display: inline-block;
        }
        
        .roll-label {
            font-size: 9px;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
        }
        
        .roll-value {
            font-size: 14px;
            color: #fef08a;
            font-weight: 700;
        }
        
        .card-footer {
            position: absolute;
            bottom: 10px;
            left: 16px;
            right: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        
        .validity {
            font-size: 11px;
            color: #ffffff;
        }
        
        .validity strong {
            color: #fef08a;
        }
        
        .signature-area {
            text-align: right;
            font-size: 9px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .signature-line {
            border-top: 1px solid rgba(255, 255, 255, 0.6);
            width: 80px;
            margin-bottom: 3px;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">ID Card Preview</h1>
                </div>
                <div class="flex items-center space-x-4">
                    @if(auth()->guard('student')->check())
                        {{-- Student viewing their own card --}}
                        <a href="{{ route('student.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            ← Back to Dashboard
                        </a>
                        <a href="{{ route('student.documents.download.idcard') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download PDF
                        </a>
                    @else
                        {{-- Admin/Staff viewing student card --}}
                        <a href="{{ route('admin.students.show', $student->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            ← Back to Student
                        </a>
                        <a href="{{ route('admin.documents.download.idcard', $student->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download PDF
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Preview Area -->
    <div class="max-w-4xl mx-auto py-12 px-4">
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6 text-center">Student ID Card Preview</h2>
            
            <!-- ID Card Preview -->
            <div class="id-card-container">
                <div class="id-card {{ $student->institute_id == 1 ? 'id-card-tech' : 'id-card-para' }}">
                    <!-- Watermark Logo -->
                    <div class="watermark-logo">
                        @if($student->institute_id == 1)
                            <img src="{{ asset('images/logos/MJPITM.png') }}" alt="Institute Logo">
                        @else
                            <img src="{{ asset('images/logos/MJPIPS.png') }}" alt="Institute Logo">
                        @endif
                    </div>
                    
                    <div class="card-content">
                    <!-- Header -->
                    <div class="card-header">
                        <div class="institute-name {{ $student->institute_id == 1 ? 'institute-name-tech' : 'institute-name-para' }}">
                            {{ $student->institute->name ?? ($student->institute_id == 1 ? 'Mahatma Jyotiba Phule Institute of Technology & Management' : 'Mahatma Jyotiba Phule Institute of Paramedical Science') }}
                        </div>
                        <div class="card-title">Student Identity Card</div>
                    </div>
                    
                    <!-- Body -->
                    <div class="card-body">
                        <div class="photo-box">
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="Photo">
                            @else
                                <div class="photo-placeholder">PHOTO</div>
                            @endif
                        </div>
                        
                        <div class="info-section">
                            <div class="name-roll-row">
                                <div class="student-name">{{ $student->name }}</div>
                                <div class="roll-box">
                                    <span class="roll-label">Roll No: </span>
                                    <span class="roll-value">{{ $student->roll_number }}</span>
                                </div>
                            </div>
                            
                            <div class="info-row" style="margin-top: 5px;">
                                <span class="info-label">Duration: </span>
                                <span class="info-value">{{ $student->course->formatted_duration ?? '3 Years' }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Father's Name: </span>
                                <span class="info-value">{{ $student->father_name ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Course: </span>
                                <span class="info-value">{{ $student->course->name ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Session: </span>
                                <span class="info-value">{{ $student->session ?? ($student->admission_year . '-' . ($student->admission_year + 1)) }}</span>
                            </div>
                        </div>
                    </div>
                    </div><!-- end card-content -->
                </div>
            </div>
        </div>

        <!-- Student Details Summary -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-md font-semibold text-gray-700 mb-4">ID Card Details</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Name</p>
                    <p class="font-medium">{{ $student->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Roll Number</p>
                    <p class="font-medium">{{ $student->roll_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Course</p>
                    <p class="font-medium">{{ $student->course->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Duration</p>
                    <p class="font-medium">{{ $student->course->formatted_duration ?? '3 Years' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Institute</p>
                    <p class="font-medium">{{ $student->institute->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Session</p>
                    <p class="font-medium">{{ $student->session ?? ($student->admission_year . '-' . ($student->admission_year + 1)) }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Valid Till</p>
                    <p class="font-medium">
                        @if($student->admission_year && $student->course)
                            @php
                                $totalMonths = $student->course->total_months ?? 36;
                                $endYear = $student->admission_year + floor($totalMonths / 12);
                            @endphp
                            {{ $endYear }}
                        @else
                            {{ date('Y', strtotime('+3 years')) }}
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
                    <p class="font-medium text-green-600">{{ ucfirst($student->status) }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

