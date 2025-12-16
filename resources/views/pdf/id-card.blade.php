<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student ID Card - {{ $student->name ?? 'Student' }}</title>
    <style>
        @page {
            margin: 5mm;
            size: 100mm 65mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.4;
        }
        
        .id-card {
            width: 90mm;
            height: 55mm;
            background: {{ $student->institute_id == 1 ? '#1e40af' : '#047857' }};
            position: relative;
            overflow: hidden;
            padding: 4mm;
        }
        
        .watermark-logo {
            position: absolute;
            top: 50%;
            right: 5mm;
            transform: translateY(-50%);
            width: 40mm;
            height: 40mm;
            opacity: 0.12;
        }
        
        .watermark-logo img {
            width: 100%;
            height: 100%;
        }
        
        .card-content {
            position: relative;
            z-index: 1;
        }
        
        .card-header {
            background: #ffffff;
            border-radius: 2mm;
            padding: 2.5mm 3mm;
            margin-bottom: 3mm;
            text-align: center;
        }
        
        .institute-name {
            font-size: 7px;
            font-weight: bold;
            color: {{ $student->institute_id == 1 ? '#1e40af' : '#047857' }};
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.3;
        }
        
        .card-title {
            font-size: 10px;
            font-weight: bold;
            color: #dc2626;
            margin-top: 1.5mm;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .card-body-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .photo-cell {
            width: 24mm;
            vertical-align: top;
        }
        
        .photo-box {
            width: 22mm;
            height: 28mm;
            background: #ffffff;
            border: 1.5mm solid #ffffff;
            border-radius: 2mm;
            overflow: hidden;
            text-align: center;
        }
        
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-placeholder {
            width: 100%;
            height: 28mm;
            background: #f3f4f6;
            color: #9ca3af;
            font-size: 8px;
            text-align: center;
            padding-top: 12mm;
        }
        
        .info-cell {
            vertical-align: top;
            padding-left: 4mm;
        }
        
        .name-roll-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2mm;
        }
        
        .name-cell {
            vertical-align: middle;
        }
        
        .roll-cell {
            vertical-align: middle;
            text-align: right;
        }
        
        .student-name {
            font-size: 12px;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
        }
        
        .info-row {
            margin-bottom: 1mm;
        }
        
        .info-label {
            font-size: 7px;
            color: #d1d5db;
            text-transform: uppercase;
        }
        
        .info-value {
            font-size: 9px;
            color: #ffffff;
            font-weight: bold;
        }
        
        .roll-box {
            background: rgba(255, 255, 255, 0.25);
            padding: 1mm 2mm;
            border-radius: 1mm;
            display: inline-block;
        }
        
        .roll-label {
            font-size: 6px;
            color: #d1d5db;
            text-transform: uppercase;
        }
        
        .roll-value {
            font-size: 10px;
            color: #fef08a;
            font-weight: bold;
        }
        
        .card-footer {
            position: absolute;
            bottom: 2mm;
            left: 4mm;
            right: 4mm;
        }
        
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .validity-cell {
            font-size: 7px;
            color: #ffffff;
        }
        
        .validity-cell strong {
            color: #fef08a;
        }
        
        .signature-cell {
            text-align: right;
            font-size: 6px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .signature-line {
            border-top: 0.5px solid rgba(255, 255, 255, 0.6);
            width: 18mm;
            margin-left: auto;
            margin-bottom: 1mm;
        }
    </style>
</head>
<body>
    <div class="id-card">
        <!-- Watermark Logo -->
        <div class="watermark-logo">
            @php
                $logoPath = $student->institute_id == 1 
                    ? public_path('images/logos/MJPITM.png') 
                    : public_path('images/logos/MJPIPS.png');
            @endphp
            @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="">
            @endif
        </div>
        
        <div class="card-content">
        <!-- Header with Institute Name -->
        <div class="card-header">
            <div class="institute-name">
                @if($student->institute)
                    {{ $student->institute->name }}
                @else
                    {{ $student->institute_id == 1 ? 'Mahatma Jyotiba Phule Institute of Technology & Management' : 'Mahatma Jyotiba Phule Institute of Paramedical Science' }}
                @endif
            </div>
            <div class="card-title">Student Identity Card</div>
        </div>
        
        <!-- Body with Photo and Info -->
        <table class="card-body-table">
            <tr>
                <td class="photo-cell">
                    <div class="photo-box">
                        @if($student->photo && file_exists(public_path('storage/' . $student->photo)))
                            <img src="{{ public_path('storage/' . $student->photo) }}" alt="Photo">
                        @else
                            <div class="photo-placeholder">PHOTO</div>
                        @endif
                    </div>
                </td>
                <td class="info-cell">
                    <table class="name-roll-table">
                        <tr>
                            <td class="name-cell">
                                <div class="student-name">{{ $student->name ?? 'N/A' }}</div>
                            </td>
                            <td class="roll-cell">
                                <div class="roll-box">
                                    <span class="roll-label">Roll No: </span>
                                    <span class="roll-value">{{ $student->roll_number ?? 'N/A' }}</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="info-row" style="margin-top: 1.5mm;">
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
                </td>
            </tr>
        </table>
        </div><!-- end card-content -->
    </div>
</body>
</html>

