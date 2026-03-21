<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Cum Details Marks Certificate - {{ $semesterResult->student?->name ?? 'Student' }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #0f172a;
            background: #fff;
        }
        /* One A4 page: header/footer positions stay fixed; body flexes between them */
        .marksheet-container {
            width: 100%;
            min-height: 297mm;
            height: 297mm;
            border: 1px solid #cbd5e1;
            position: relative;
            background: #fff;
            padding: 14mm 14mm 12mm;
            box-sizing: border-box;
            overflow: hidden;
        }
        .watermark {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
            opacity: 0.06;
        }
        .watermark img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 160mm;
            height: auto;
            opacity: 1;
        }
        /* Reserve space so flowing text does not sit under the page-bottom footer */
        .content { position: relative; z-index: 1; padding-bottom: 96mm; }
        .sheet-header {
            position: relative;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4mm;
            margin-bottom: 5mm;
        }
        .sheet-body {
            position: relative;
            padding-bottom: 0;
            clear: both;
        }
        /* Positioned vs .marksheet-container (full A4 inner box), not .content */
        .sheet-footer {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 82mm;
            z-index: 2;
            padding-top: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            background: #fff;
        }
        .header { display: none; } /* replaced with .sheet-header */
        .institute-logo { margin: 12px 0; }
        .institute-logo img {
            max-height: 100px;
            max-width: 280px;
        }
        .logo-placeholder {
            height: 85px;
            width: 280px;
            margin: 0 auto;
        }
        .institute-name-hindi {
            font-size: 13px;
            font-weight: bold;
            margin: 6px 0;
            color: #111827;
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
            display: none;
        }
        .accreditation-line { margin: 2px 0; }
        .accreditation-placeholder {
            height: 28px;
            width: 100%;
        }
        .cert-title {
            font-size: 15px;
            font-weight: 700;
            margin-top: 8px;
            letter-spacing: 0.1em;
            color: #0f172a;
            text-transform: uppercase;
        }
        .cert-underline {
            width: 200px;
            height: 2px;
            background: #1e3a5f;
            margin: 4px auto 0;
            opacity: 0.9;
        }
        .sr-no-top {
            position: absolute;
            top: 10px;
            right: 12px;
            font-size: 9px;
            font-weight: bold;
            color: #111827;
        }
        .examination-session-line {
            font-size: 9px;
            color: #64748b;
            margin-top: 6px;
            margin-bottom: 6px;
            font-weight: 500;
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
            margin: 12px 0 12px;
            padding: 10px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: visible;
        }
        .student-details > table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .student-details > table > tbody > tr > td:first-child { padding-right: 12px; vertical-align: top; }
        .student-details > table > tbody > tr > td:last-child { padding-left: 6px; vertical-align: top; }
        .student-details table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .student-details td { padding: 4px 4px 4px 6px; font-size: 9.4px; border: none; vertical-align: top; line-height: 1.4; }
        .field-label { font-weight: 600; width: 42%; color: #334155; white-space: nowrap; font-size: 9px; }
        .field-value { width: 58%; color: #0f172a; font-weight: 500; word-wrap: break-word; overflow-wrap: break-word; }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .marks-table th {
            background: #f1f5f9;
            color: #334155;
            padding: 7px 5px;
            font-size: 8.5px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border: 1px solid #e2e8f0;
        }
        .marks-table td {
            padding: 6px 5px;
            font-size: 9px;
            text-align: center;
            border: 1px solid #e2e8f0;
            background: #fff;
            vertical-align: middle;
        }
        .marks-table tbody tr:nth-child(even) td { background: #fafbfc; }
        .marks-table td:first-child,
        .marks-table td:nth-child(2) {
            text-align: left;
            padding-left: 10px;
        }
        .marks-table td:nth-child(2) { font-weight: 600; color: #1e293b; }
        .total-row {
            background: #f1f5f9 !important;
            color: #0f172a;
            font-weight: 700;
        }
        .total-row td {
            border: 1px solid #e2e8f0;
            background: #f1f5f9 !important;
        }
        .result-summary {
            margin: 8px 0 0;
            padding: 0;
            background: transparent;
            border: none;
            border-radius: 0;
        }
        .result-summary table { width: 100%; border-collapse: collapse; }
        .result-summary td {
            padding: 3px 4px;
            font-size: 9px;
            border: none;
        }
        .result-summary td:first-child {
            font-weight: bold;
            width: 28%;
            color: #111827;
        }
        .result-summary .highlight { font-size: 9px; font-weight: normal; color: #111827; }
        .grading-notes {
            font-size: 7.5px;
            margin: 0 0 6px 0;
            line-height: 1.6;
            color: #111827;
        }
        .grading-notes ol { margin-left: 18px; }
        .footer {
            display: none; /* replaced with .sheet-footer */
        }
        .signature-section { display: none; }
        .footer-meta {
            margin-bottom: 0.8mm;
            color: #111827;
        }
        .footer-top-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 8mm;
        }
        .footer-top-row .footer-meta {
            flex: 1 1 auto;
            max-width: 70%;
            margin-bottom: 0;
        }
        .controller-signature {
            flex: 0 0 30%;
            text-align: right;
            color: #111827;
            padding-bottom: 1mm;
        }
        .controller-signature .sign-label {
            font-family: "Times New Roman", Georgia, serif;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.02em;
            line-height: 1.2;
        }
        .footer-meta .grading-notes {
            margin: 0 0 1mm 0;
            font-size: 8px;
            line-height: 1.2;
        }
        .footer-meta .grading-notes ol { margin-left: 15px; }
        .footer-meta-summary {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.4px;
            line-height: 1.1;
        }
        .footer-meta-summary td {
            padding: 0 2px;
            border: none;
            color: #111827;
        }
        .footer-meta-summary td:first-child { width: 24%; font-weight: bold; }
        .footer-logos-wrap {
            width: 100%;
            text-align: center;
        }
        .footer-logos-table {
            display: inline-table;
            width: auto;
            margin: 0 auto;
            border-collapse: separate;
            border-spacing: 2px 0;
            table-layout: auto;
        }
        .footer-logos-table td {
            width: auto;
            padding: 0;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }
        .footer-badge-img {
            max-height: 28px;
            max-width: 64px;
            width: auto;
            height: auto;
            object-fit: contain;
            vertical-align: middle;
        }
        .footer-logo-slot {
            display: inline-block;
            width: 56px;
            height: 28px;
            border: 1px dashed #94a3b8;
            border-radius: 3px;
            background: #f8fafc;
        }
    </style>
</head>
<body>
    <div class="marksheet-container">
        <!-- Watermark removed -->

        @php
            $inst = $semesterResult->student?->institute;
            $headerLogoPath = $inst?->marksheet_header_logo ? public_path('storage/' . $inst->marksheet_header_logo) : null;
            $watermarkPath = $inst?->marksheet_watermark_image ? public_path('storage/' . $inst->marksheet_watermark_image) : null;
            $footer1Path = $inst?->marksheet_footer_logo_1 ? public_path('storage/' . $inst->marksheet_footer_logo_1) : null;
            $footer2Path = $inst?->marksheet_footer_logo_2 ? public_path('storage/' . $inst->marksheet_footer_logo_2) : null;
            $footer3Path = $inst?->marksheet_footer_logo_3 ? public_path('storage/' . $inst->marksheet_footer_logo_3) : null;
            $footer4Path = $inst?->marksheet_footer_logo_4 ? public_path('storage/' . $inst->marksheet_footer_logo_4) : null;
            $instId = $semesterResult->student?->institute_id ?? 1;
            $fallbackInstituteLogo = public_path('images/logos/' . ($instId == 1 ? 'MJPITM.png' : 'MJPIPS.png'));
            $demoHeader = public_path('images/marksheet-placeholders/header-demo.svg');
            $demoWatermark = public_path('images/marksheet-placeholders/watermark-demo.svg');
            $demoFooter1 = public_path('images/marksheet-placeholders/footer-demo-1.svg');
            $demoFooter2 = public_path('images/marksheet-placeholders/footer-demo-2.svg');
            $demoFooter3 = public_path('images/marksheet-placeholders/footer-demo-3.svg');
            $demoFooter4 = public_path('images/marksheet-placeholders/footer-demo-4.svg');
            $watermarkImgSrc = ($watermarkPath && file_exists($watermarkPath))
                ? $watermarkPath
                : (file_exists($demoWatermark) ? $demoWatermark : null);
        @endphp

        @if($watermarkImgSrc)
            <div class="watermark">
                <img src="{{ $watermarkImgSrc }}" alt="Watermark">
            </div>
        @endif

        <div class="content">
            <div class="sr-no-top">Sr. No. {{ $semesterResult->formatted_marksheet_serial }}</div>

            <div class="sheet-header">
                @php
                    $finalHeaderLogo = ($headerLogoPath && file_exists($headerLogoPath))
                        ? $headerLogoPath
                        : (file_exists($fallbackInstituteLogo) ? $fallbackInstituteLogo : (file_exists($demoHeader) ? $demoHeader : null));
                @endphp
                @if($finalHeaderLogo)
                    <div class="institute-logo" style="margin: 0 0 4mm 0;">
                        <img src="{{ $finalHeaderLogo }}" alt="Header Logo">
                    </div>
                @else
                    <div class="logo-placeholder"></div>
                @endif

                <div style="height: 8mm;"></div>

                <div class="cert-title">Result Cum Details Marks Certificate</div>
                <div class="examination-session-line">
                    Examination session {{ $semesterResult->academic_year ? '(' . $semesterResult->academic_year . ')' : '—' }}
                </div>
                <div class="cert-underline"></div>
            </div>

            <div class="sheet-body">

            <!-- Student Details (two columns like reference) -->
            <div class="student-details">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-right: 16px;">
                            <table style="width: 100%; border: none;">
                                <tr><td class="field-label">Student’s Name :</td><td class="field-value"><strong>{{ strtoupper($semesterResult->student?->name ?? 'N/A') }}</strong></td></tr>
                                <tr><td class="field-label">Father’s Name :</td><td class="field-value">{{ strtoupper($semesterResult->student?->father_name ?? 'N/A') }}</td></tr>
                                <tr><td class="field-label">Course :</td><td class="field-value"><strong>{{ strtoupper($semesterResult->course?->name ?? 'N/A') }}</strong></td></tr>
                                <tr><td class="field-label">Institute :</td><td class="field-value">{{ strtoupper($semesterResult->student?->institute?->name ?? (($semesterResult->student?->institute_id ?? 1) == 1 ? 'Mahatma Jyotiba Phule Institute of Technology & Management' : 'Mahatma Jyotiba Phule Institute of Paramedical Science')) }}</td></tr>
                            </table>
                        </td>
                        <td style="width: 50%; vertical-align: top;">
                            <table style="width: 100%; border: none;">
                                <tr><td class="field-label">Enrollment No. :</td><td class="field-value">{{ $semesterResult->student?->roll_number ?? $semesterResult->student?->registration_number ?? 'N/A' }}</td></tr>
                                <tr><td class="field-label">Date of Birth :</td><td class="field-value">{{ $semesterResult->student?->date_of_birth ? display_date($semesterResult->student->date_of_birth) : 'N/A' }}</td></tr>
                                <tr><td class="field-label">Mother’s name :</td><td class="field-value">{{ strtoupper($semesterResult->student?->mother_name ?? 'N/A') }}</td></tr>
                                <tr><td class="field-label">Semester/Year :</td><td class="field-value">
                                    @php
                                        $sem = (int) $semesterResult->semester;
                                        $yearNum = (int) ceil($sem / 2);
                                        $yearOrd = $yearNum == 1 ? '1ST' : ($yearNum == 2 ? '2ND' : ($yearNum == 3 ? '3RD' : $yearNum . 'TH'));
                                    @endphp
                                    {{ $yearOrd }} YEAR
                                </td></tr>
                            </table>
                        </td>
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
                    @foreach($semesterResult->results ?? [] as $index => $result)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ strtoupper($result->subject?->name ?? '—') }}</strong></td>
                            <td>{{ number_format($result->total_marks ?? 0, 2) }}</td>
                            <td>{{ number_format($result->theory_marks_obtained ?? 0, 2) }}</td>
                            <td>{{ number_format($result->practical_marks_obtained ?? 0, 2) }}</td>
                            <td><strong>{{ number_format($result->marks_obtained ?? 0, 2) }}</strong></td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2"><strong>TOTAL</strong></td>
                        <td><strong>{{ number_format($semesterResult->total_max_marks ?? 0, 2) }}</strong></td>
                        <td><strong>{{ number_format($semesterResult->results?->sum('theory_marks_obtained') ?? 0, 2) }}</strong></td>
                        <td><strong>{{ number_format($semesterResult->results?->sum('practical_marks_obtained') ?? 0, 2) }}</strong></td>
                        <td><strong>{{ number_format($semesterResult->total_marks_obtained ?? 0, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            </div> <!-- /sheet-body -->

        </div> <!-- /content -->

        <div class="sheet-footer">
            <div class="footer-top-row">
                <div class="footer-meta">
                    <div class="grading-notes">
                        <ol>
                            <li>Line below the marks indicates failure in the paper</li>
                            <li>The minimum marks for: Pass Marks 40%</li>
                            <li>Second Division 48%</li>
                            <li>First Division 55%</li>
                        </ol>
                    </div>
                    <table class="footer-meta-summary">
                        <tr>
                            <td>Percentage:</td>
                            <td>{{ number_format($semesterResult->overall_percentage ?? 0, 2) }}%</td>
                        </tr>
                        <tr>
                            <td>Division:</td>
                            <td>
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
                            <td>Result declaration:</td>
                            <td>{{ $semesterResult->result_declaration_date ? display_date($semesterResult->result_declaration_date, '—') : '—' }}</td>
                        </tr>
                        <tr>
                            <td>Date of issue:</td>
                            <td>{{ $semesterResult->date_of_issue ? display_date($semesterResult->date_of_issue, '—') : '—' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="controller-signature">
                    <div class="sign-label">Controller of Examination</div>
                </div>
            </div>
            <div class="footer-logos-wrap">
            @php
                $fallbackFooter = file_exists($fallbackInstituteLogo) ? $fallbackInstituteLogo : null;
                $resolveFooter = function (?string $uploaded, string $demo, ?string $fallback) {
                    if ($uploaded && file_exists($uploaded)) {
                        return $uploaded;
                    }
                    if ($fallback && file_exists($fallback)) {
                        return $fallback;
                    }
                    return file_exists($demo) ? $demo : null;
                };
                $footer1Resolved = $resolveFooter($footer1Path, $demoFooter1, $fallbackFooter);
                $footer2Resolved = $resolveFooter($footer2Path, $demoFooter2, $fallbackFooter);
                $footer3Resolved = $resolveFooter($footer3Path, $demoFooter3, $fallbackFooter);
                $footer4Resolved = $resolveFooter($footer4Path, $demoFooter4, $fallbackFooter);
            @endphp
            <table class="footer-logos-table" role="presentation">
                <tr>
                    <td>
                        @if($footer1Resolved)
                            <img class="footer-badge-img" src="{{ $footer1Resolved }}" alt="Footer 1">
                        @else
                            <span class="footer-logo-slot"></span>
                        @endif
                    </td>
                    <td>
                        @if($footer2Resolved)
                            <img class="footer-badge-img" src="{{ $footer2Resolved }}" alt="Footer 2">
                        @else
                            <span class="footer-logo-slot"></span>
                        @endif
                    </td>
                    <td>
                        @if($footer3Resolved)
                            <img class="footer-badge-img" src="{{ $footer3Resolved }}" alt="Footer 3">
                        @else
                            <span class="footer-logo-slot"></span>
                        @endif
                    </td>
                    <td>
                        @if($footer4Resolved)
                            <img class="footer-badge-img" src="{{ $footer4Resolved }}" alt="Footer 4">
                        @else
                            <span class="footer-logo-slot"></span>
                        @endif
                    </td>
                </tr>
            </table>
            </div>
        </div>
    </div>
</body>
</html>
