<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Semester Result - {{ $semesterResult->student->name }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #111827;
        }
        .header {
            background: {{ $semesterResult->student->institute_id == 1 ? 'linear-gradient(135deg, #1e40af 0%, #3b82f6 100%)' : 'linear-gradient(135deg, #047857 0%, #10b981 100%)' }};
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
            position: relative;
        }
        .logo-container {
            margin-bottom: 10px;
        }
        .logo-container img {
            max-height: 50px;
            max-width: 180px;
        }
        .institute-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 8px;
            text-transform: uppercase;
        }
        .academic-year {
            font-size: 12px;
            margin-top: 5px;
            opacity: 0.95;
        }
        .student-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 6px 10px;
            font-size: 11px;
        }
        .student-info td:first-child {
            font-weight: bold;
            width: 30%;
            color: #374151;
        }
        .student-info td:last-child {
            color: #111827;
        }
        .results-section {
            margin: 20px 0;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: {{ $semesterResult->student->institute_id == 1 ? '#1e40af' : '#047857' }};
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid {{ $semesterResult->student->institute_id == 1 ? '#1e40af' : '#047857' }};
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th {
            background: {{ $semesterResult->student->institute_id == 1 ? '#1e40af' : '#047857' }};
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid {{ $semesterResult->student->institute_id == 1 ? '#1e3a8a' : '#065f46' }};
        }
        td {
            border: 1px solid #d1d5db;
            padding: 10px 8px;
            font-size: 11px;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .overall {
            background-color: {{ $semesterResult->student->institute_id == 1 ? '#dbeafe' : '#d1fae5' }} !important;
            font-weight: bold;
            font-size: 12px;
        }
        .overall-percentage {
            font-size: 16px;
            color: {{ $semesterResult->student->institute_id == 1 ? '#1e40af' : '#047857' }};
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        .signatures {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        .signature {
            display: table-cell;
            width: 45%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 2px solid #111827;
            width: 70%;
            margin: 35px auto 8px;
        }
        .signature-label {
            font-size: 11px;
            font-weight: bold;
            color: #374151;
        }
        .watermark {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            opacity: 0.08;
            font-size: 120px;
            font-weight: bold;
            color: white;
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="watermark">RESULT</div>
        <div class="logo-container" style="position: relative; z-index: 1;">
            @php
                $logoPath = $semesterResult->student->institute_id == 1 
                    ? public_path('images/logos/MJPITM.png') 
                    : public_path('images/logos/MJPIPS.png');
            @endphp
            @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Institute Logo">
            @endif
        </div>
        <div class="institute-name">
            {{ $semesterResult->student->institute->name ?? ($semesterResult->student->institute_id == 1 ? 'Mahatma Jyotiba Phule Institute of Technology & Management' : 'Mahatma Jyotiba Phule Institute of Paramedical Science') }}
        </div>
        <div class="title">SEMESTER {{ $semesterResult->semester }} RESULT</div>
        <div class="academic-year">Academic Year: {{ $semesterResult->academic_year }}</div>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td>Student Name:</td>
                <td><strong>{{ $semesterResult->student->name }}</strong></td>
                <td>Roll Number:</td>
                <td><strong>{{ $semesterResult->student->roll_number ?? $semesterResult->student->registration_number }}</strong></td>
            </tr>
            <tr>
                <td>Course:</td>
                <td>{{ $semesterResult->course->name }}</td>
                <td>Semester:</td>
                <td>{{ $semesterResult->semester }}</td>
            </tr>
            <tr>
                <td>Academic Year:</td>
                <td>{{ $semesterResult->academic_year }}</td>
                <td>Date of Issue:</td>
                <td>{{ $semesterResult->published_at->format('d M Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="results-section">
        <div class="section-title">Subject-wise Results</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">S.No.</th>
                    <th style="width: 35%;">Subject Name</th>
                    <th style="width: 12%;">Code</th>
                    <th style="width: 12%;">Theory</th>
                    <th style="width: 12%;">Practical</th>
                    <th style="width: 12%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($semesterResult->results as $index => $result)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $result->subject->name }}</strong></td>
                        <td class="text-center">{{ $result->subject->code }}</td>
                        <td class="text-center">{{ $result->theory_marks_obtained ?? 0 }} / {{ $result->subject->theory_marks ?? 0 }}</td>
                        <td class="text-center">{{ $result->practical_marks_obtained ?? 0 }} / {{ $result->subject->practical_marks ?? 0 }}</td>
                        <td class="text-center"><strong>{{ $result->marks_obtained ?? 0 }} / {{ $result->total_marks ?? 0 }}</strong></td>
                    </tr>
                @endforeach
                <tr class="overall">
                    <td colspan="5" class="text-right" style="padding-right: 15px;"><strong>OVERALL:</strong></td>
                    <td class="text-center">
                        <strong>{{ number_format($semesterResult->total_marks_obtained, 2) }} / {{ number_format($semesterResult->total_max_marks, 2) }}</strong>
                        <div class="overall-percentage" style="margin-top: 5px;">
                            <strong>{{ number_format($semesterResult->overall_percentage ?? 0, 2) }}%</strong>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="signatures">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Controller of Examinations</div>
            </div>
            <div class="signature" style="width: 10%;"></div>
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Principal</div>
            </div>
        </div>
    </div>
</body>
</html>
