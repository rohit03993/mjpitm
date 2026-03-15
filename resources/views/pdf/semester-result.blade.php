<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Cum Details Marks Certificate - {{ $semesterResult->student->name }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1f2937;
            background: #fff;
        }
        .marksheet-container {
            width: 100%;
            min-height: 100vh;
            border: 3px solid #1e3a5f;
            position: relative;
            background: #fff;
            padding: 18mm;
        }
        .watermark {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            opacity: 0.03;
            z-index: 0;
            pointer-events: none;
        }
        .watermark-text {
            position: absolute;
            font-size: 72px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 4px;
            top: 50%; left: 50%;
            margin-top: -24px; margin-left: -180px;
        }
        .content { position: relative; z-index: 1; }
        .header {
            text-align: center;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 2px solid #1e3a5f;
        }
        .institute-logo { margin: 12px 0; }
        .institute-logo img {
            max-height: 100px;
            max-width: 280px;
        }
        .institute-name-hindi {
            font-size: 13px;
            font-weight: bold;
            margin: 6px 0;
            color: #1e3a5f;
            letter-spacing: 0.5px;
        }
        .institute-name-english {
            font-size: 11px;
            font-weight: bold;
            margin: 4px 0;
            color: #334155;
            letter-spacing: 1px;
        }
        .accreditation {
            font-size: 6.5px;
            line-height: 1.35;
            margin: 8px 0;
            text-align: center;
            color: #64748b;
        }
        .accreditation-line { margin: 2px 0; }
        .cert-title {
            font-size: 15px;
            font-weight: bold;
            margin-top: 14px;
            letter-spacing: 2px;
            color: #1e3a5f;
            text-transform: uppercase;
        }
        .sr-no-top {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 10px;
            font-weight: bold;
            color: #475569;
        }
        .examination-session-line {
            font-size: 9px;
            color: #475569;
            margin-top: 6px;
            margin-bottom: 10px;
        }
        .examination-details {
            margin: 14px 0;
            padding: 12px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #1e3a5f;
        }
        .examination-details table { width: 100%; border-collapse: collapse; }
        .examination-details td {
            padding: 6px 10px;
            font-size: 9px;
            border: none;
        }
        .examination-details td:first-child {
            font-weight: bold;
            width: 28%;
            color: #475569;
        }
        .examination-details td:last-child {
            font-weight: bold;
            color: #1e3a5f;
            font-size: 10px;
        }
        .student-details {
            margin: 14px 0;
            padding: 0;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }
        .student-details table { width: 100%; border-collapse: collapse; }
        .student-details tr:nth-child(odd) { background: #fff; }
        .student-details tr:nth-child(even) { background: #f1f5f9; }
        .student-details td {
            padding: 6px 12px;
            font-size: 9px;
            border: none;
            border-bottom: 1px solid #e2e8f0;
        }
        .student-details td:first-child {
            font-weight: bold;
            width: 28%;
            color: #475569;
        }
        .section-label {
            background: #1e3a5f;
            color: #fff;
            padding: 6px 12px;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            border: 1px solid #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }
        .marks-table th {
            background: #0d9488;
            color: #fff;
            padding: 9px 6px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .marks-table td {
            padding: 7px 6px;
            font-size: 9px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .marks-table tbody tr:nth-child(even) { background: #f8fafc; }
        .marks-table td:first-child,
        .marks-table td:nth-child(2) {
            text-align: left;
            padding-left: 12px;
        }
        .marks-table td:nth-child(2) { font-weight: bold; color: #334155; }
        .total-row {
            background: #1e3a5f !important;
            color: #fff;
            font-weight: bold;
        }
        .total-row td { border-bottom: none; }
        .result-summary {
            margin: 14px 0;
            padding: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 2px;
        }
        .result-summary table { width: 100%; border-collapse: collapse; }
        .result-summary td {
            padding: 6px 10px;
            font-size: 9px;
            border: none;
        }
        .result-summary td:first-child {
            font-weight: bold;
            width: 28%;
            color: #475569;
        }
        .result-summary .highlight { font-size: 11px; font-weight: bold; color: #1e3a5f; }
        .grading-notes {
            font-size: 7.5px;
            margin: 0 0 12px 0;
            line-height: 1.6;
            color: #64748b;
        }
        .grading-notes ol { margin-left: 18px; }
        .footer {
            margin-top: 22px;
            padding-top: 16px;
            border-top: 2px solid #e2e8f0;
        }
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 18px;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-seal {
            margin: 0 auto 6px;
            width: 72px;
            height: 72px;
            border: 2px solid #1e3a5f;
            border-radius: 50%;
            font-size: 6.5px;
            text-align: center;
            background: #f8fafc;
            padding: 8px;
        }
        .signature-label {
            font-size: 8px;
            font-weight: bold;
            color: #475569;
            letter-spacing: 0.5px;
        }
        .logos-section {
            display: table;
            width: 100%;
            margin-top: 16px;
            padding: 12px 0;
        }
        .logo-item {
            display: table-cell;
            text-align: center;
            width: 33%;
        }
        .logo-item img { max-height: 36px; max-width: 70px; }
        .logo-item-text { font-size: 6.5px; margin-top: 3px; font-weight: bold; color: #64748b; }
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
            <div class="sr-no-top">Sr. No. {{ str_pad((string) $semesterResult->id, 8, '0', STR_PAD_LEFT) }}</div>
            <!-- Header -->
            <div class="header">
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

                <div class="cert-title">Result Cum Details Marks Certificate</div>
                <div class="examination-session-line">
                    @php
                        $ay = $semesterResult->academic_year;
                        if ($ay && preg_match('/^(\d{4})-\d{2}$/', $ay, $m)) {
                            $y1 = (int) $m[1];
                            $y2 = $y1 + 1;
                            echo 'Examination session: JULY ' . $y1 . ' - JUNE ' . $y2;
                        } else {
                            echo 'Examination session: —';
                        }
                    @endphp
                </div>
                <div style="width: 60px; height: 2px; background: #1e3a5f; margin: 8px auto 0;"></div>
            </div>

            <!-- Student Details (two columns like reference) -->
            <div class="student-details">
                <div class="section-label">Candidate &amp; Programme Details</div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-right: 16px;">
                            <table style="width: 100%; border: none;">
                                <tr><td style="font-weight: bold; color: #475569; font-size: 9px; padding: 4px 0;">Roll No.:</td><td style="font-size: 9px;">{{ $semesterResult->student->roll_number ?? $semesterResult->student->registration_number ?? 'N/A' }}</td></tr>
                                <tr><td style="font-weight: bold; color: #475569; font-size: 9px; padding: 4px 0;">Student's Name:</td><td style="font-size: 9px;"><strong>{{ strtoupper($semesterResult->student->name) }}</strong></td></tr>
                                <tr><td style="font-weight: bold; color: #475569; font-size: 9px; padding: 4px 0;">Father's Name:</td><td style="font-size: 9px;">{{ strtoupper($semesterResult->student->father_name ?? 'N/A') }}</td></tr>
                                <tr><td style="font-weight: bold; color: #475569; font-size: 9px; padding: 4px 0;">Course:</td><td style="font-size: 9px;"><strong>{{ strtoupper($semesterResult->course->name) }}</strong></td></tr>
                                <tr><td style="font-weight: bold; color: #475569; font-size: 9px; padding: 4px 0;">Institute:</td><td style="font-size: 9px;">{{ strtoupper($semesterResult->student->institute->name ?? ($semesterResult->student->institute_id == 1 ? 'Mahatma Jyotiba Phule Institute of Technology & Management' : 'Mahatma Jyotiba Phule Institute of Paramedical Science')) }}</td></tr>
                            </table>
                        </td>
                        <td style="width: 50%; vertical-align: top;">
                            <table style="width: 100%; border: none;">
                                <tr><td style="font-weight: bold; color: #475569; font-size: 9px; padding: 4px 0;">Enrollment No.:</td><td style="font-size: 9px;">{{ $semesterResult->student->registration_number ?? $semesterResult->student->roll_number ?? 'N/A' }}</td></tr>
                                <tr><td style="font-weight: bold; color: #475569; font-size: 9px; padding: 4px 0;">Date of Birth:</td><td style="font-size: 9px;">{{ $semesterResult->student->date_of_birth ? \Carbon\Carbon::parse($semesterResult->student->date_of_birth)->format('d/m/Y') : 'N/A' }}</td></tr>
                                <tr><td style="font-weight: bold; color: #475569; font-size: 9px; padding: 4px 0;">Mother's name:</td><td style="font-size: 9px;">{{ strtoupper($semesterResult->student->mother_name ?? 'N/A') }}</td></tr>
                                <tr><td style="font-weight: bold; color: #475569; font-size: 9px; padding: 4px 0;">Semester/Year:</td><td style="font-size: 9px;">
                                    @php
                                        $sem = (int) $semesterResult->semester;
                                        $yearNum = (int) ceil($sem / 2);
                                        $yearOrd = $yearNum == 1 ? '1ST' : ($yearNum == 2 ? '2ND' : ($yearNum == 3 ? '3RD' : $yearNum . 'TH'));
                                    @endphp
                                    SEMESTER {{ $sem }}, {{ $yearOrd }} YEAR
                                </td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Marks Table -->
            <div class="section-label" style="margin-top: 18px;">Statement of Marks</div>
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
                        <li>The minimum marks for : Pass Marks 40%</li>
                        <li>Second Division 48%</li>
                        <li>First Division 55%</li>
                    </ol>
                </div>
                <table style="margin-top: 10px;">
                    <tr>
                        <td>Percentage:</td>
                        <td class="highlight">{{ number_format($semesterResult->overall_percentage ?? 0, 2) }}%</td>
                    </tr>
                    <tr>
                        <td>Division:</td>
                        <td class="highlight">
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
                        </td>
                    </tr>
                    <tr>
                        <td>Date of issue:</td>
                        <td class="highlight">{{ $semesterResult->date_of_issue ? 'Date: ' . \Carbon\Carbon::parse($semesterResult->date_of_issue)->format('d F Y') : '—' }}</td>
                    </tr>
                </table>
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
