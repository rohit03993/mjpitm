<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provisional Registration Form</title>
    @php
        $isTech = ($student->institute_id ?? 1) == 1;
        $themePrimary = $isTech ? '#1e40af' : '#166534';
        $themeLight = $isTech ? '#dbeafe' : '#dcfce7';
    @endphp
    <style>
        /* A4 portrait, 8mm margins – content area 194mm x 281mm */
        @page { margin: 8mm; size: A4 portrait; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8pt;
            color: #374151;
            line-height: 1.25;
        }
        table { border-collapse: collapse; width: 100%; }
        .header-table {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 5px;
            background: #fafafa;
        }
        .header-table td { vertical-align: middle; padding: 0; }
        .logo-cell { width: 11%; padding-right: 6px; }
        .logo-cell img { max-width: 52px; max-height: 52px; display: block; }
        .institute-cell { width: 89%; }
        .institute-name {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin: 0 0 1px 0;
            line-height: 1.2;
            color: {{ $themePrimary }};
        }
        .institute-address { font-size: 7pt; text-align: center; margin: 0; color: #555; }
        .main-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 5px 0 4px 0;
            letter-spacing: 0.5px;
            border: 1px solid #d1d5db;
            padding: 6px 10px;
            background: #fff;
            color: #111827;
        }
        .regno-top {
            margin: 0 0 5px 0;
            padding: 5px 10px;
            border: 1px solid {{ $themePrimary }};
            background: {{ $themeLight }};
            text-align: center;
            font-weight: bold;
            color: {{ $themePrimary }};
        }
        .regno-top .label { font-size: 6.5pt; text-transform: uppercase; margin-bottom: 0; }
        .regno-top .value { font-size: 10pt; letter-spacing: 0.5px; }
        .course-box {
            border: 1px solid #e5e7eb;
            padding: 6px;
            margin: 4px 0;
            position: relative;
            min-height: 82px;
            background: #fafafa;
        }
        .course-box table { width: 68%; }
        .course-box td { padding: 2px 0; border: none; font-size: 7.5pt; vertical-align: top; }
        .course-label { font-weight: bold; padding-right: 5px; color: #4b5563; }
        .photo-box {
            position: absolute;
            right: 6px;
            top: 6px;
            width: 64px;
            height: 76px;
            border: 1px solid #d1d5db;
            padding: 2px;
            text-align: center;
            background: #fff;
        }
        .photo-box img { width: 100%; height: calc(100% - 10px); object-fit: cover; display: block; }
        .photo-label { font-size: 5.5pt; font-weight: bold; margin-top: 0; }
        .section-header {
            background-color: {{ $themePrimary }};
            color: white;
            padding: 4px 6px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin: 5px 0 0 0;
        }
        .info-table { border: 1px solid #e5e7eb; }
        .info-table td {
            border: 1px solid #e5e7eb;
            padding: 4px 6px;
            width: 50%;
            vertical-align: top;
            min-height: 20px;
            font-size: 7.5pt;
        }
        .field-label { font-weight: bold; font-size: 7pt; color: {{ $themePrimary }}; display: block; margin-bottom: 0; }
        .field-value { font-size: 7.5pt; text-transform: uppercase; color: #111827; }
        .qual-table { border: 1px solid #e5e7eb; margin-top: 3px; }
        .qual-table th, .qual-table td {
            border: 1px solid #e5e7eb;
            padding: 3px 4px;
            text-align: center;
            font-size: 7pt;
        }
        .qual-table th { background-color: #f3f4f6; font-weight: bold; color: #374151; }
        .declaration-box p {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            margin: 0 0 3px 0;
            font-size: 6.5pt;
            text-align: justify;
            line-height: 1.4;
            background: #fafafa;
        }
        .signature-section {
            margin-top: 6px;
            padding: 5px;
            border: 1px solid #e5e7eb;
            background: #fafafa;
        }
        .signature-section > div:first-child { font-size: 6.5pt; font-weight: bold; margin-bottom: 2px; }
        .signature-box {
            display: inline-block;
            width: 100px;
            height: 36px;
            border: 1px solid #d1d5db;
            padding: 2px;
            margin-top: 2px;
            text-align: center;
            background: #fff;
        }
        .signature-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .signature-label { font-size: 5.5pt; font-weight: bold; margin-top: 0; }
        .office-use {
            margin-top: 6px;
            border: 1px solid #e5e7eb;
            padding: 5px 8px;
            background: #fafafa;
            font-size: 6.5pt;
            color: #6b7280;
        }
        .office-use .title { font-weight: bold; margin-bottom: 2px; color: #374151; }
        .footer {
            margin-top: 6px;
            padding-top: 5px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 6.5pt;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @php
                    $instituteId = $student->institute_id ?? null;
                    $logoBase64 = null;
                    $logoPath = null;
                    
                    // Optimize: Check only one file per institute (prefer PNG, fallback to JPG)
                    if ($instituteId == 1) {
                        $logoPath = file_exists(public_path('images/logos/MJPITM.png')) 
                            ? public_path('images/logos/MJPITM.png')
                            : (file_exists(public_path('images/logos/MJPITM.jpg')) 
                                ? public_path('images/logos/MJPITM.jpg') 
                                : null);
                    } elseif ($instituteId == 2) {
                        $logoPath = file_exists(public_path('images/logos/MJPIPS.png')) 
                            ? public_path('images/logos/MJPIPS.png')
                            : (file_exists(public_path('images/logos/MJPIPS.jpg')) 
                                ? public_path('images/logos/MJPIPS.jpg') 
                                : null);
                    }
                    
                    if ($logoPath) {
                        $logoExtension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                        $logoMimeType = ($logoExtension === 'png') ? 'image/png' : 'image/jpeg';
                        $logoBase64 = 'data:' . $logoMimeType . ';base64,' . base64_encode(file_get_contents($logoPath));
                    }
                @endphp
                @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo" style="border: 1px solid {{ $themePrimary }}; border-radius: 4px; padding: 1px; background: #fff;">
                @else
                <div style="width: 52px; height: 52px; border: 1px solid {{ $themePrimary }}; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 7pt; background: {{ $themePrimary }}; color: #fff; font-weight: bold; text-align: center;">
                    {{ substr($student->institute->name ?? 'MJP', 0, 3) }}
                </div>
                @endif
            </td>
            <td class="institute-cell">
                <div class="institute-name">{{ $student->institute->name ?? 'Mahatma Jyotiba Phule Institutes' }}</div>
                <div class="institute-address">
                    @if($student->institute)
                        @if($student->institute->domain == 'mjpitm.in')
                            Technology & Management Programs
                        @elseif($student->institute->domain == 'mjpips.in')
                            Paramedical & Healthcare Programs
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Title -->
    <div class="main-title">Provisional Registration Form</div>

    <!-- Registration Number (on top) -->
    <div class="regno-top">
        <div class="label">Registration No.</div>
        <div class="value">{{ $student->registration_number ?? 'N/A' }}</div>
    </div>

    <!-- Course Details Box -->
    <div class="course-box">
        <table>
            <tr>
                <td class="course-label">SESSION:</td>
                <td>
                    @if($student->session)
                        {{ $student->session }}
                    @elseif($student->admission_year)
                        {{ $student->admission_year }}-{{ substr($student->admission_year + 1, -2) }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <td class="course-label">COURSE:</td>
                <td>{{ strtoupper($student->course->name ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="course-label">STREAM:</td>
                <td>{{ strtoupper($student->stream ?? 'GENERAL') }}</td>
            </tr>
            <tr>
                <td class="course-label">YEAR:</td>
                <td>{{ $student->current_semester ?? '1' }}</td>
            </tr>
        </table>
        
        @if($student->photo)
        <div class="photo-box">
            @php
                // Optimize: Check storage path first, then public path
                $photoPath = file_exists(storage_path('app/public/' . $student->photo))
                    ? storage_path('app/public/' . $student->photo)
                    : (file_exists(public_path('storage/' . $student->photo))
                        ? public_path('storage/' . $student->photo)
                        : null);
                $photoBase64 = null;
                if ($photoPath) {
                    $photoExtension = strtolower(pathinfo($photoPath, PATHINFO_EXTENSION));
                    $photoMimeType = ($photoExtension === 'png') ? 'image/png' : 'image/jpeg';
                    $photoBase64 = 'data:' . $photoMimeType . ';base64,' . base64_encode(file_get_contents($photoPath));
                }
            @endphp
            @if($photoBase64)
            <img src="{{ $photoBase64 }}" alt="Photo">
            @else
            <div style="width: 100%; height: calc(100% - 12px); display: table-cell; vertical-align: middle; text-align: center; background: #f0f0f0; color: #666; font-size: 5px;">
                Photo Not Available
            </div>
            @endif
            <div class="photo-label">Passport Size Photo</div>
        </div>
        @endif
    </div>

    <!-- General Information -->
    <div class="section-header">General Information</div>
    <table class="info-table">
        <tr>
            <td>
                <span class="field-label">Name of the Candidate:</span>
                <div class="field-value"><strong>{{ strtoupper($student->name) }}</strong></div>
            </td>
            <td>
                <span class="field-label">Date Of Birth:</span>
                <div class="field-value">{{ $student->date_of_birth ? $student->date_of_birth->format('d-m-Y') : 'N/A' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="field-label">Father's Name:</span>
                <div class="field-value">{{ strtoupper($student->father_name ?? 'N/A') }}</div>
            </td>
            <td>
                <span class="field-label">Mother Name:</span>
                <div class="field-value">{{ strtoupper($student->mother_name ?? 'N/A') }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="field-label">Nationality:</span>
                <div class="field-value">{{ strtoupper($student->nationality ?? 'INDIAN') }}</div>
            </td>
            <td>
                <span class="field-label">Aadhaar No:</span>
                <div class="field-value">{{ $student->aadhaar_number ?? 'N/A' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="field-label">Passport No:</span>
                <div class="field-value">{{ $student->passport_number ?? 'N/A' }}</div>
            </td>
            <td>
                <span class="field-label">Category:</span>
                <div class="field-value">{{ strtoupper($student->category ?? 'GENERAL') }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="field-label">Gender:</span>
                <div class="field-value">{{ strtoupper($student->gender ?? 'N/A') }}</div>
            </td>
            <td>
                <span class="field-label">Admission Type:</span>
                <div class="field-value">{{ strtoupper($student->admission_type ?? 'NORMAL') }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="field-label">Year:</span>
                <div class="field-value">{{ $student->current_semester ?? '1' }}</div>
            </td>
            <td>
                <span class="field-label">Contact Number:</span>
                <div class="field-value">{{ $student->phone ?? 'N/A' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="field-label">Email Address:</span>
                <div class="field-value">{{ strtolower($student->email ?? 'N/A') }}</div>
            </td>
            <td>
                <span class="field-label">Candidate Address:</span>
                <div class="field-value">{{ strtoupper($student->address ?? 'N/A') }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="field-label">District:</span>
                <div class="field-value">{{ strtoupper($student->district ?? 'N/A') }}</div>
            </td>
            <td>
                <span class="field-label">Pin Code:</span>
                <div class="field-value">{{ $student->pin_code ?? 'N/A' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="field-label">State:</span>
                <div class="field-value">{{ strtoupper($student->state ?? 'N/A') }}</div>
            </td>
            <td>
                <span class="field-label">Country:</span>
                <div class="field-value">{{ strtoupper($student->country ?? 'INDIA') }}</div>
            </td>
        </tr>
    </table>

    <!-- Qualification Information -->
    @if($student->qualifications && $student->qualifications->count() > 0)
    <div class="section-header">Qualification Information</div>
    <table class="qual-table">
        <thead>
            <tr>
                <th style="width: 20%;">Examination</th>
                <th style="width: 15%;">Year</th>
                <th style="width: 35%;">Board/University</th>
                <th style="width: 15%;">Marks(%)</th>
                <th style="width: 15%;">Subjects</th>
            </tr>
        </thead>
        <tbody>
            @foreach($student->qualifications as $qualification)
            <tr>
                <td>{{ strtoupper(str_replace('_', ' ', $qualification->examination ?? 'N/A')) }}</td>
                <td>{{ $qualification->year_of_passing ?? 'N/A' }}</td>
                <td>{{ strtoupper($qualification->board_university ?? 'N/A') }}</td>
                <td>{{ $qualification->percentage ?? ($qualification->cgpa ?? 'N/A') }}</td>
                <td>{{ strtoupper($qualification->subjects ?? 'N/A') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Declaration -->
    <div class="section-header">Declaration</div>
    <div class="declaration-box">
        <p>I hereby declare that the entries made and documents submitted by me in this registration form are true to the best of my knowledge and belief. I understand that if any information provided by me is found to be false or incorrect, my registration shall be automatically cancelled and all fees paid by me shall be forfeited. The institute/university reserves the right to take any other action as deemed fit. I further declare that my registration is subject to the rules and regulations of the institute/university. I agree to abide by all discipline and conduct rules. I am aware that ragging is banned and if found guilty I shall be liable to be punished as per the law.</p>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div>Signature of the Candidate:</div>
        @if($student->signature)
        <div class="signature-box">
            @php
                // Optimize: Check storage path first, then public path
                $signaturePath = file_exists(storage_path('app/public/' . $student->signature))
                    ? storage_path('app/public/' . $student->signature)
                    : (file_exists(public_path('storage/' . $student->signature))
                        ? public_path('storage/' . $student->signature)
                        : null);
                $signatureBase64 = null;
                if ($signaturePath) {
                    $signatureExtension = strtolower(pathinfo($signaturePath, PATHINFO_EXTENSION));
                    $signatureMimeType = ($signatureExtension === 'png') ? 'image/png' : 'image/jpeg';
                    $signatureBase64 = 'data:' . $signatureMimeType . ';base64,' . base64_encode(file_get_contents($signaturePath));
                }
            @endphp
            @if($signatureBase64)
            <img src="{{ $signatureBase64 }}" alt="Signature">
            @else
            <div style="width: 100%; height: 100%; display: table-cell; vertical-align: middle; text-align: center; background: #f0f0f0; color: #666; font-size: 5px;">
                Signature Not Available
            </div>
            @endif
        </div>
        <div class="signature-label">Signature</div>
        @else
        <div class="signature-box" style="background: #f0f0f0;">
            <div style="width: 100%; height: 100%; display: table-cell; vertical-align: middle; text-align: center; color: #666; font-size: 5px;">
                Signature Not Available
            </div>
        </div>
        @endif
    </div>

    <div class="office-use">
        <div class="title">For office use only</div>
        <div>Remarks: ___________________________ &nbsp; Verified by: ________________ &nbsp; Date: ________</div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Registration No: <strong>{{ $student->registration_number ?? 'N/A' }}</strong> | Generated on {{ now()->format('d M Y, h:i A') }}</p>
        <p>{{ $student->institute->name ?? 'Mahatma Jyotiba Phule Institutes' }}</p>
    </div>
</body>
</html>
