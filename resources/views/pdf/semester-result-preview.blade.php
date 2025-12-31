<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semester Result - {{ $semesterResult->student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .result-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: {{ $semesterResult->student->institute_id == 1 ? 'linear-gradient(135deg, #1e40af 0%, #3b82f6 100%)' : 'linear-gradient(135deg, #047857 0%, #10b981 100%)' }};
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        .logo-container {
            margin-bottom: 15px;
        }
        .logo-container img {
            max-height: 60px;
            max-width: 200px;
        }
        .institute-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }
        .academic-year {
            font-size: 14px;
            margin-top: 8px;
            opacity: 0.9;
        }
        .student-info {
            padding: 25px;
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }
        .results-section {
            padding: 25px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: {{ $semesterResult->student->institute_id == 1 ? '#1e40af' : '#047857' }};
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid {{ $semesterResult->student->institute_id == 1 ? '#1e40af' : '#047857' }};
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: {{ $semesterResult->student->institute_id == 1 ? '#1e40af' : '#047857' }};
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }
        tbody tr:hover {
            background: #f9fafb;
        }
        .overall-row {
            background: {{ $semesterResult->student->institute_id == 1 ? '#dbeafe' : '#d1fae5' }} !important;
            font-weight: bold;
            font-size: 14px;
        }
        .overall-percentage {
            font-size: 20px;
            color: {{ $semesterResult->student->institute_id == 1 ? '#1e40af' : '#047857' }};
        }
        .footer {
            padding: 25px;
            background: #f9fafb;
            border-top: 2px solid #e5e7eb;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            border-top: 2px solid #111827;
            width: 80%;
            margin: 40px auto 10px;
        }
        .signature-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: {{ $semesterResult->student->institute_id == 1 ? '#1e40af' : '#047857' }};
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .print-button:hover {
            opacity: 0.9;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Print / Save as PDF</button>
    
    <div class="result-container">
        <!-- Header with Logo and Institute Name -->
        <div class="header">
            <div class="logo-container">
                @php
                    $logoPath = $semesterResult->student->institute_id == 1 
                        ? asset('images/logos/MJPITM.png') 
                        : asset('images/logos/MJPIPS.png');
                @endphp
                @if(file_exists(public_path('images/logos/' . ($semesterResult->student->institute_id == 1 ? 'MJPITM.png' : 'MJPIPS.png'))))
                    <img src="{{ $logoPath }}" alt="Institute Logo">
                @endif
            </div>
            <div class="institute-name">
                {{ $semesterResult->student->institute->name ?? ($semesterResult->student->institute_id == 1 ? 'Mahatma Jyotiba Phule Institute of Technology & Management' : 'Mahatma Jyotiba Phule Institute of Paramedical Science') }}
            </div>
            <div class="title">Semester {{ $semesterResult->semester }} Result</div>
            <div class="academic-year">Academic Year: {{ $semesterResult->academic_year }}</div>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Student Name</span>
                    <span class="info-value">{{ $semesterResult->student->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Roll Number</span>
                    <span class="info-value">{{ $semesterResult->student->roll_number ?? $semesterResult->student->registration_number }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Course</span>
                    <span class="info-value">{{ $semesterResult->course->name }}</span>
                </div>
            </div>
        </div>

        <!-- Results Table -->
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
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td><strong>{{ $result->subject->name }}</strong></td>
                            <td style="text-align: center;">{{ $result->subject->code }}</td>
                            <td style="text-align: center;">{{ $result->theory_marks_obtained ?? 0 }} / {{ $result->subject->theory_marks ?? 0 }}</td>
                            <td style="text-align: center;">{{ $result->practical_marks_obtained ?? 0 }} / {{ $result->subject->practical_marks ?? 0 }}</td>
                            <td style="text-align: center;"><strong>{{ $result->marks_obtained ?? 0 }} / {{ $result->total_marks ?? 0 }}</strong></td>
                        </tr>
                    @endforeach
                    <tr class="overall-row">
                        <td colspan="5" style="text-align: right; padding-right: 20px;"><strong>OVERALL:</strong></td>
                        <td style="text-align: center;">
                            <strong>{{ number_format($semesterResult->total_marks_obtained, 2) }} / {{ number_format($semesterResult->total_max_marks, 2) }}</strong>
                            <div class="overall-percentage" style="margin-top: 5px;">
                                <strong>{{ number_format($semesterResult->overall_percentage ?? 0, 2) }}%</strong>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer with Signatures -->
        <div class="footer">
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Controller of Examinations</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Principal</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

