<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Cum Details Marks Certificate - {{ $semesterResult->student->name }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }
        .marksheet-container {
            width: 100%;
            min-height: 100vh;
            border: 8px solid #87CEEB;
            position: relative;
            background: #fff;
            padding: 15mm;
        }
        .watermark {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            z-index: 0;
            pointer-events: none;
        }
        .watermark-text {
            position: absolute;
            font-size: 80px;
            font-weight: bold;
            color: #000;
            transform: rotate(-45deg);
            white-space: nowrap;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
        }
        .content {
            position: relative;
            z-index: 1;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .serial-number {
            text-align: right;
            font-size: 9px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .institute-logo {
            margin: 10px 0;
        }
        .institute-logo img {
            max-height: 60px;
            max-width: 200px;
        }
        .institute-name-hindi {
            font-size: 14px;
            font-weight: bold;
            margin: 8px 0;
            text-transform: uppercase;
        }
        .institute-name-english {
            font-size: 12px;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .accreditation {
            font-size: 7px;
            line-height: 1.3;
            margin: 8px 0;
            text-align: center;
        }
        .accreditation-line {
            margin: 2px 0;
        }
        .examination-details {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #000;
        }
        .examination-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .examination-details td {
            padding: 4px 8px;
            font-size: 9px;
            border: 1px solid #000;
        }
        .examination-details td:first-child {
            font-weight: bold;
            width: 25%;
            background: #f0f0f0;
        }
        .student-details {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #000;
        }
        .student-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-details td {
            padding: 4px 8px;
            font-size: 9px;
            border: 1px solid #000;
        }
        .student-details td:first-child {
            font-weight: bold;
            width: 25%;
            background: #f0f0f0;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border: 1px solid #000;
        }
        .marks-table th {
            background: #1e40af;
            color: white;
            padding: 8px 5px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
        }
        .marks-table td {
            padding: 6px 5px;
            font-size: 9px;
            text-align: center;
            border: 1px solid #000;
        }
        .marks-table td:first-child,
        .marks-table td:nth-child(2) {
            text-align: left;
            padding-left: 8px;
        }
        .total-row {
            background: #e0e0e0;
            font-weight: bold;
        }
        .result-summary {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #000;
        }
        .result-summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .result-summary td {
            padding: 4px 8px;
            font-size: 9px;
            border: 1px solid #000;
        }
        .result-summary td:first-child {
            font-weight: bold;
            width: 30%;
            background: #f0f0f0;
        }
        .grading-notes {
            font-size: 8px;
            margin: 10px 0;
            line-height: 1.5;
        }
        .grading-notes ol {
            margin-left: 20px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #000;
        }
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-seal {
            margin: 10px auto;
            width: 80px;
            height: 80px;
            border: 2px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            text-align: center;
            background: #f9f9f9;
        }
        .signature-label {
            font-size: 9px;
            font-weight: bold;
            margin-top: 5px;
        }
        .logos-section {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-top: 15px;
            padding: 10px 0;
        }
        .logo-item {
            text-align: center;
        }
        .logo-item img {
            max-height: 40px;
            max-width: 80px;
        }
        .logo-item-text {
            font-size: 7px;
            margin-top: 3px;
            font-weight: bold;
        }
        .date-section {
            text-align: right;
            margin-top: 10px;
            font-size: 9px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="marksheet-container">
        <!-- Watermark -->
        <div class="watermark">
            <div class="watermark-text">
                @php
                    $instituteName = $semesterResult->student->institute->name ?? ($semesterResult->student->institute_id == 1 ? 'MAHATMA JYOTIBA PHULE INSTITUTE OF TECHNOLOGY & MANAGEMENT' : 'MAHATMA JYOTIBA PHULE INSTITUTE OF PARAMEDICAL SCIENCE');
                @endphp
                {{ strtoupper($instituteName) }} {{ strtoupper($instituteName) }} {{ strtoupper($instituteName) }}
            </div>
        </div>

        <div class="content">
            <!-- Header -->
            <div class="header">
                <div class="serial-number">Sr. No.: {{ str_pad($semesterResult->id, 8, '0', STR_PAD_LEFT) }}</div>
                
                <div class="institute-logo">
                    @php
                        $logoPath = $semesterResult->student->institute_id == 1 
                            ? public_path('images/logos/MJPITM.png') 
                            : public_path('images/logos/MJPIPS.png');
                    @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ $logoPath }}" alt="Institute Logo">
                    @endif
                </div>

                <div class="institute-name-hindi">
                    @if($semesterResult->student->institute_id == 1)
                        महात्मा ज्योतिबा फुले प्रौद्योगिकी एवं प्रबंधन संस्थान (स्वायत्त)
                    @else
                        महात्मा ज्योतिबा फुले पराचिकित्सा संस्थान (स्वायत्त)
                    @endif
                </div>

                <div class="institute-name-english">
                    {{ $semesterResult->student->institute->name ?? ($semesterResult->student->institute_id == 1 ? 'Mahatma Jyotiba Phule Institute of Technology & Management' : 'Mahatma Jyotiba Phule Institute of Paramedical Science') }}
                </div>

                <div class="accreditation">
                    @if($semesterResult->student->institute_id == 1)
                        <div class="accreditation-line">An Autonomous Institution for Education & Training Run and Managed By Diksha Educational Trust, Regd. By Govt. of NCT of Delhi</div>
                        <div class="accreditation-line">Estd. & Regd. By Indian Trusts Act, 1882 under Guidelines of NEP-1986 & 2020 Incorporated under the legislation of Govt of India</div>
                        <div class="accreditation-line">Affiliated with Labour Ministry Govt of India, NITI Aayog In association with MoEAn ISO 9001:2015 Certified Institution</div>
                    @else
                        <div class="accreditation-line">An Autonomous Institution for Education & Training Run and Managed By Diksha Educational Trust, Regd. By Govt. of NCT of Delhi</div>
                        <div class="accreditation-line">Estd. & Regd. By Indian Trusts Act, 1882 under Guidelines of NEP-1986 & 2020 Incorporated under the legislation of Govt of India</div>
                        <div class="accreditation-line">Affiliated with Labour Ministry Govt of India, NITI Aayog In association with MoEAn ISO 9001:2015 Certified Institution</div>
                    @endif
                </div>

                <div style="font-size: 16px; font-weight: bold; margin-top: 10px; text-transform: uppercase;">
                    Result Cum Details Marks Certificate
                </div>
            </div>

            <!-- Examination Details -->
            <div class="examination-details">
                <table>
                    <tr>
                        <td>Examination session:</td>
                        <td>
                            @php
                                $sessionParts = explode('-', $semesterResult->academic_year);
                                $startYear = $sessionParts[0];
                                $endYear = isset($sessionParts[1]) ? '20' . $sessionParts[1] : ($startYear + 1);
                            @endphp
                            JULY {{ $startYear }} - JUNE {{ $endYear }}
                        </td>
                    </tr>
                    <tr>
                        <td>Roll No.:</td>
                        <td><strong>{{ $semesterResult->student->roll_number ?? $semesterResult->student->registration_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>Enrollment No:</td>
                        <td>{{ $semesterResult->student->registration_number ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            <!-- Student Details -->
            <div class="student-details">
                <table>
                    <tr>
                        <td>Student's Name:</td>
                        <td><strong>{{ strtoupper($semesterResult->student->name) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Date of Birth:</td>
                        <td>{{ $semesterResult->student->date_of_birth ? \Carbon\Carbon::parse($semesterResult->student->date_of_birth)->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Father's Name:</td>
                        <td>{{ strtoupper($semesterResult->student->father_name ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td>Mother's name:</td>
                        <td>{{ strtoupper($semesterResult->student->mother_name ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td>Course:</td>
                        <td><strong>{{ strtoupper($semesterResult->course->name) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Semester/Year:</td>
                        <td>{{ $semesterResult->semester }}{{ $semesterResult->semester == 1 ? 'ST' : ($semesterResult->semester == 2 ? 'ND' : ($semesterResult->semester == 3 ? 'RD' : 'TH')) }} YEAR</td>
                    </tr>
                    <tr>
                        <td>Institute:</td>
                        <td>{{ strtoupper($semesterResult->student->institute->name ?? ($semesterResult->student->institute_id == 1 ? 'Mahatma Jyotiba Phule Institute of Technology & Management' : 'Mahatma Jyotiba Phule Institute of Paramedical Science')) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Marks Table -->
            <table class="marks-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">Sr. No.</th>
                        <th style="width: 35%;">Subject</th>
                        <th style="width: 12%;">Max. Marks</th>
                        <th style="width: 12%;">Theory</th>
                        <th style="width: 12%;">Practical</th>
                        <th style="width: 12%;">Obt. Marks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($semesterResult->results as $index => $result)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ strtoupper($result->subject->name) }}</strong></td>
                            <td>{{ number_format($result->total_marks ?? 0, 2) }}</td>
                            <td>{{ number_format($result->theory_marks_obtained ?? 0, 2) }}</td>
                            <td>{{ number_format($result->practical_marks_obtained ?? 0, 2) }}</td>
                            <td><strong>{{ number_format($result->marks_obtained ?? 0, 2) }}</strong></td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2"><strong>TOTAL</strong></td>
                        <td><strong>{{ number_format($semesterResult->total_max_marks, 2) }}</strong></td>
                        <td><strong>{{ number_format($semesterResult->results->sum('theory_marks_obtained'), 2) }}</strong></td>
                        <td><strong>{{ number_format($semesterResult->results->sum('practical_marks_obtained'), 2) }}</strong></td>
                        <td><strong>{{ number_format($semesterResult->total_marks_obtained, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <!-- Result Summary -->
            <div class="result-summary">
                <div class="grading-notes">
                    <ol>
                        <li>Line below the marks indicates failure in the paper</li>
                        <li>The minimum marks for: Pass Marks 40%</li>
                        <li>Second Division 48%</li>
                        <li>First Division 55%</li>
                    </ol>
                </div>
                <table style="margin-top: 10px;">
                    <tr>
                        <td>Percentage:</td>
                        <td><strong>{{ number_format($semesterResult->overall_percentage ?? 0, 2) }}%</strong></td>
                    </tr>
                    <tr>
                        <td>Division:</td>
                        <td>
                            <strong>
                                @php
                                    $percentage = $semesterResult->overall_percentage ?? 0;
                                    if ($percentage >= 55) {
                                        echo 'First';
                                    } elseif ($percentage >= 48) {
                                        echo 'Second';
                                    } elseif ($percentage >= 40) {
                                        echo 'Pass';
                                    } else {
                                        echo 'Fail';
                                    }
                                @endphp
                            </strong>
                        </td>
                    </tr>
                </table>
                <div class="date-section">
                    Date: {{ ($semesterResult->published_at ?? now())->format('d F Y') }}
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-seal">
                            <div style="text-align: center; padding: 5px;">
                                <div style="font-weight: bold; margin-bottom: 3px;">CONTROLLER</div>
                                <div style="font-size: 6px;">OF</div>
                                <div style="font-weight: bold; margin-top: 3px;">EXAMINATION</div>
                            </div>
                        </div>
                        <div class="signature-label">Controller of Examination</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-seal">
                            <div style="text-align: center; padding: 5px;">
                                <div style="font-weight: bold;">PRINCIPAL</div>
                            </div>
                        </div>
                        <div class="signature-label">Principal</div>
                    </div>
                </div>

                <!-- Logos Section -->
                <div class="logos-section">
                    <div class="logo-item">
                        <div style="font-size: 8px; font-weight: bold; margin-bottom: 3px;">कौशल भारत-कुशल भारत</div>
                        <div style="font-size: 7px;">Skill India</div>
                    </div>
                    <div class="logo-item">
                        <div style="font-size: 8px; font-weight: bold; margin-bottom: 3px;">CERTIFIED</div>
                        <div style="font-size: 7px;">ISO 9001:2015</div>
                    </div>
                    <div class="logo-item">
                        <div style="font-size: 7px; font-weight: bold; margin-bottom: 3px;">स्वच्छ भारत</div>
                        <div style="font-size: 6px;">एक कदम स्वच्छता की ओर</div>
                        <div style="font-size: 7px; margin-top: 2px;">Swachh Bharat</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
