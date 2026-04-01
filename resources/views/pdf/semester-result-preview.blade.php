<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Cum Details Marks Certificate - {{ $semesterResult->student?->name ?? 'Student' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, Tahoma, Geneva, Verdana, sans-serif;
            background: #e2e8f0;
            padding: 24px;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }
        .marksheet-container {
            max-width: 210mm;
            width: 210mm;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08), 0 1px 0 rgba(15, 23, 42, 0.04);
            border: 1px solid #cbd5e1;
            border-radius: 2px;
            position: relative;
            min-height: 297mm;
            height: 297mm;
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
            overflow: hidden;
        }
        .watermark img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 160mm;
            height: auto;
        }
        /* Bottom padding reserves space for absolutely positioned footer (sibling, not child) */
        .content { position: relative; z-index: 1; padding: 14mm 14mm 96mm; }
        /* No fixed height — fixed height made border-bottom cut through title/session when content grew */
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
        /* Anchored to .marksheet-container (A4), not .content — otherwise logos sit under the summary */
        .sheet-footer {
            position: absolute;
            left: 14mm;
            right: 14mm;
            bottom: 8mm;
            height: 82mm;
            z-index: 2;
            padding-top: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            background: #fff;
        }
        .header { display: none; }
        .institute-logo { margin: 12px 0; }
        .institute-logo img { max-height: 100px; max-width: 280px; }
        .logo-placeholder {
            height: 85px;
            width: 280px;
            margin: 0 auto;
        }
        .institute-name-hindi {
            font-size: 14px;
            font-weight: bold;
            margin: 6px 0;
            color: #111827;
            letter-spacing: 0.5px;
        }
        .institute-name-english {
            font-size: 12px;
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
            font-size: 17px;
            font-weight: 700;
            margin-top: 8px;
            letter-spacing: 0.12em;
            color: #0f172a;
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
            font-size: 10px;
            color: #64748b;
            margin-top: 8px;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .cert-underline {
            width: 200px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #1e3a5f 20%, #1e3a5f 80%, transparent);
            border-radius: 1px;
            margin: 4px auto 0;
            opacity: 0.85;
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
            font-size: 10px;
            border: none;
        }
        .examination-details td:first-child { font-weight: bold; width: 28%; color: #475569; }
        .examination-details td:last-child { font-weight: bold; color: #1e3a5f; font-size: 11px; }
        .student-details {
            margin: 12px 0 12px;
            padding: 12px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: visible;
        }
        .student-details > table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .student-details > table > tbody > tr > td:first-child { padding-right: 14px; vertical-align: top; }
        .student-details > table > tbody > tr > td:last-child { padding-left: 6px; vertical-align: top; }
        .student-details table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .student-details td { padding: 5px 4px 5px 6px; font-size: 10px; border: none; vertical-align: top; line-height: 1.45; }
        .field-label { font-weight: 600; width: 42%; color: #334155; font-size: 9.5px; white-space: nowrap; }
        .field-value { width: 58%; color: #0f172a; font-weight: 500; word-wrap: break-word; overflow-wrap: break-word; }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 12px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .marks-table th {
            background: #f1f5f9;
            color: #334155;
            padding: 8px 6px;
            font-size: 8.8px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border: 1px solid #e2e8f0;
        }
        .marks-table td {
            padding: 7px 6px;
            font-size: 9.5px;
            text-align: center;
            border: 1px solid #e2e8f0;
            background: #fff;
            vertical-align: middle;
        }
        .marks-table tbody tr:nth-child(even) td { background: #fafbfc; }
        .marks-table td:first-child,
        .marks-table td:nth-child(2) { text-align: left; padding-left: 12px; }
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
        .result-summary td:first-child { font-weight: bold; width: 28%; color: #111827; }
        .result-summary .highlight { font-size: 9px; font-weight: normal; color: #111827; }
        .grading-notes {
            font-size: 8px;
            margin: 0 0 6px 0;
            line-height: 1.6;
            color: #111827;
        }
        .grading-notes ol { margin-left: 18px; }
        .footer { display: none; }
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
            font-size: 8.4px;
            line-height: 1.2;
        }
        .footer-meta .grading-notes ol { margin-left: 16px; }
        .footer-meta-summary {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.7px;
            line-height: 1.1;
        }
        .footer-meta-summary td {
            padding: 0 2px;
            border: none;
            color: #111827;
        }
        .footer-meta-summary td:first-child { width: 24%; font-weight: bold; }
        /* Tight cluster: max 2px between icons, whole group centered (not stretched full width) */
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
            max-height: 34px;
            max-width: 76px;
            width: auto;
            height: auto;
            object-fit: contain;
            vertical-align: middle;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.12));
        }
        .footer-logo-slot {
            display: inline-block;
            width: 64px;
            height: 34px;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            background: #f8fafc;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1e3a5f;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(30, 58, 95, 0.25);
            z-index: 1000;
        }
        .print-button:hover { background: #16325a; }
        @media print {
            body { background: #fff; padding: 0; }
            .print-button { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Print / Save as PDF</button>
    
    <div class="marksheet-container">
        <!-- Watermark removed -->

        @php
            $inst = $semesterResult->student?->institute;
            $headerLogoUrl = $inst?->marksheet_header_logo ? asset('storage/' . $inst->marksheet_header_logo) : null;
            $headerLogoPath = $inst?->marksheet_header_logo ? public_path('storage/' . $inst->marksheet_header_logo) : null;
            $watermarkUrl = $inst?->marksheet_watermark_image ? asset('storage/' . $inst->marksheet_watermark_image) : null;
            $watermarkPath = $inst?->marksheet_watermark_image ? public_path('storage/' . $inst->marksheet_watermark_image) : null;
            $footer1Url = $inst?->marksheet_footer_logo_1 ? asset('storage/' . $inst->marksheet_footer_logo_1) : null;
            $footer1Path = $inst?->marksheet_footer_logo_1 ? public_path('storage/' . $inst->marksheet_footer_logo_1) : null;
            $footer2Url = $inst?->marksheet_footer_logo_2 ? asset('storage/' . $inst->marksheet_footer_logo_2) : null;
            $footer2Path = $inst?->marksheet_footer_logo_2 ? public_path('storage/' . $inst->marksheet_footer_logo_2) : null;
            $footer3Url = $inst?->marksheet_footer_logo_3 ? asset('storage/' . $inst->marksheet_footer_logo_3) : null;
            $footer3Path = $inst?->marksheet_footer_logo_3 ? public_path('storage/' . $inst->marksheet_footer_logo_3) : null;
            $footer4Url = $inst?->marksheet_footer_logo_4 ? asset('storage/' . $inst->marksheet_footer_logo_4) : null;
            $footer4Path = $inst?->marksheet_footer_logo_4 ? public_path('storage/' . $inst->marksheet_footer_logo_4) : null;
            $instId = $semesterResult->student?->institute_id ?? 1;
            $fallbackInstituteLogoPath = public_path('images/logos/' . ($instId == 1 ? 'MJPITM.png' : 'MJPIPS.png'));
            $fallbackInstituteLogoUrl = asset('images/logos/' . ($instId == 1 ? 'MJPITM.png' : 'MJPIPS.png'));
            $demoHeaderUrl = asset('images/marksheet-placeholders/header-demo.svg');
            $demoWatermarkUrl = asset('images/marksheet-placeholders/watermark-demo.svg');
            $demoFooter1Url = asset('images/marksheet-placeholders/footer-demo-1.svg');
            $demoFooter2Url = asset('images/marksheet-placeholders/footer-demo-2.svg');
            $demoFooter3Url = asset('images/marksheet-placeholders/footer-demo-3.svg');
            $demoFooter4Url = asset('images/marksheet-placeholders/footer-demo-4.svg');
            $demoWatermarkFs = public_path('images/marksheet-placeholders/watermark-demo.svg');
            $finalWatermarkUrl = ($watermarkUrl && $watermarkPath && file_exists($watermarkPath))
                ? $watermarkUrl
                : (file_exists($demoWatermarkFs) ? $demoWatermarkUrl : null);
        @endphp

        @if($finalWatermarkUrl)
            <div class="watermark">
                <img src="{{ $finalWatermarkUrl }}" alt="Watermark">
            </div>
        @endif

        <div class="content">
            <div class="sheet-header">
                @php
                    $demoHeaderFs = public_path('images/marksheet-placeholders/header-demo.svg');
                    $finalHeaderLogoUrl = ($headerLogoUrl && $headerLogoPath && file_exists($headerLogoPath))
                        ? $headerLogoUrl
                        : (file_exists($fallbackInstituteLogoPath) ? $fallbackInstituteLogoUrl : (file_exists($demoHeaderFs) ? $demoHeaderUrl : null));
                @endphp
                @if($finalHeaderLogoUrl)
                    <div class="institute-logo" style="margin: 0 0 4mm 0;">
                        <img src="{{ $finalHeaderLogoUrl }}" alt="Header Logo">
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

            <div class="sr-no-top">Sr. No. {{ $semesterResult->formatted_marksheet_serial }}</div>

            <div class="sheet-body">
            <!-- Student Details (two columns) -->
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
                                <tr><td class="field-label">Semester/Year :</td><td class="field-value">{{ $semesterResult->marksheet_semester_year_line }}</td></tr>
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
                        <td><strong>{{ number_format($semesterResult->total_max_marks, 2) }}</strong></td>
                        <td><strong>{{ number_format($semesterResult->results?->sum('theory_marks_obtained') ?? 0, 2) }}</strong></td>
                        <td><strong>{{ number_format($semesterResult->results?->sum('practical_marks_obtained') ?? 0, 2) }}</strong></td>
                        <td><strong>{{ number_format($semesterResult->total_marks_obtained, 2) }}</strong></td>
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
                $demoF1 = public_path('images/marksheet-placeholders/footer-demo-1.svg');
                $demoF2 = public_path('images/marksheet-placeholders/footer-demo-2.svg');
                $demoF3 = public_path('images/marksheet-placeholders/footer-demo-3.svg');
                $demoF4 = public_path('images/marksheet-placeholders/footer-demo-4.svg');
                $fallbackFooterOk = $fallbackInstituteLogoPath && file_exists($fallbackInstituteLogoPath);
                $resolveFooterUrl = function (?string $url, ?string $path, string $demoUrl, string $demoFs) use ($fallbackInstituteLogoUrl, $fallbackFooterOk) {
                    if ($url && $path && file_exists($path)) {
                        return $url;
                    }
                    if ($fallbackFooterOk) {
                        return $fallbackInstituteLogoUrl;
                    }
                    return file_exists($demoFs) ? $demoUrl : null;
                };
                $footer1ResolvedUrl = $resolveFooterUrl($footer1Url, $footer1Path, $demoFooter1Url, $demoF1);
                $footer2ResolvedUrl = $resolveFooterUrl($footer2Url, $footer2Path, $demoFooter2Url, $demoF2);
                $footer3ResolvedUrl = $resolveFooterUrl($footer3Url, $footer3Path, $demoFooter3Url, $demoF3);
                $footer4ResolvedUrl = $resolveFooterUrl($footer4Url, $footer4Path, $demoFooter4Url, $demoF4);
            @endphp
            <table class="footer-logos-table" role="presentation">
                <tr>
                    <td>
                        @if($footer1ResolvedUrl)
                            <img class="footer-badge-img" src="{{ $footer1ResolvedUrl }}" alt="Footer 1">
                        @else
                            <span class="footer-logo-slot"></span>
                        @endif
                    </td>
                    <td>
                        @if($footer2ResolvedUrl)
                            <img class="footer-badge-img" src="{{ $footer2ResolvedUrl }}" alt="Footer 2">
                        @else
                            <span class="footer-logo-slot"></span>
                        @endif
                    </td>
                    <td>
                        @if($footer3ResolvedUrl)
                            <img class="footer-badge-img" src="{{ $footer3ResolvedUrl }}" alt="Footer 3">
                        @else
                            <span class="footer-logo-slot"></span>
                        @endif
                    </td>
                    <td>
                        @if($footer4ResolvedUrl)
                            <img class="footer-badge-img" src="{{ $footer4ResolvedUrl }}" alt="Footer 4">
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
