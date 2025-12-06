<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provisional Registration Form</title>
    <style>
        @page {
            margin: 8mm 10mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .header-table {
            border-bottom: 3px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        }
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        .logo-cell {
            width: 12%;
            padding-right: 12px;
            vertical-align: middle;
        }
        .logo-cell img {
            max-width: 70px;
            max-height: 70px;
            display: block;
        }
        .institute-cell {
            width: 88%;
            position: relative;
            padding-top: 3px;
        }
        .institute-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 4px;
            line-height: 1.3;
            padding: 4px 0;
            color: #1e40af;
            letter-spacing: 0.5px;
            text-shadow: 0.5px 0.5px 0px rgba(0,0,0,0.1);
        }
        .institute-address {
            font-size: 7.5px;
            text-align: center;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
            padding: 2px 0;
        }
        .contact-info {
            font-size: 6.5px;
            text-align: center;
            color: #555;
            padding: 3px 0;
            background: #f8f9fa;
            border-radius: 3px;
        }
        .contact-info span {
            margin-right: 18px;
            padding: 1px 4px;
        }
        .contact-info .phone {
            color: #ea580c;
            font-weight: bold;
        }
        .contact-info .website {
            color: #1e40af;
            font-weight: bold;
        }
        .contact-info .email {
            color: #1e40af;
            font-weight: bold;
        }
        .main-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 12px auto 8px auto;
            letter-spacing: 1px;
            border: 3px solid #000;
            padding: 8px 15px;
            background: #fff;
            display: block;
            width: 100%;
            box-sizing: border-box;
        }
        .course-box {
            border: 1px solid #000;
            padding: 5px;
            margin: 5px 0;
            position: relative;
            min-height: 120px;
        }
        .course-box table {
            width: 70%;
        }
        .course-box td {
            padding: 2px 0;
            border: none;
            font-size: 7.5px;
            vertical-align: top;
        }
        .course-label {
            font-weight: bold;
            padding-right: 5px;
        }
        .photo-box {
            position: absolute;
            right: 5px;
            top: 5px;
            width: 90px;
            height: 110px;
            border: 2px solid #000;
            padding: 2px;
            text-align: center;
        }
        .photo-box img {
            width: 100%;
            height: calc(100% - 12px);
            object-fit: cover;
            display: block;
        }
        .photo-label {
            font-size: 5.5px;
            font-weight: bold;
            margin-top: 2px;
        }
        .section-header {
            background-color: #1e40af;
            color: white;
            padding: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin: 6px 0 3px 0;
        }
        .info-table {
            border: 1px solid #000;
        }
        .info-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            width: 50%;
            vertical-align: top;
            height: 20px;
        }
        .field-label {
            font-weight: bold;
            font-size: 7px;
            color: #1e40af;
            display: block;
            margin-bottom: 1px;
        }
        .field-value {
            font-size: 7.5px;
            text-transform: uppercase;
            padding-top: 1px;
        }
        .qual-table {
            border: 1px solid #000;
            margin-top: 3px;
        }
        .qual-table th,
        .qual-table td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            font-size: 7px;
        }
        .qual-table th {
            background-color: #e5e7eb;
            font-weight: bold;
        }
        .declaration-box p {
            border: 1px solid #000;
            padding: 3px;
            margin-bottom: 4px;
            font-size: 6.5px;
            text-align: justify;
            line-height: 1.4;
        }
        .signature-section {
            margin-top: 8px;
            padding: 5px;
            border: 1px solid #000;
        }
        .signature-box {
            display: inline-block;
            width: 150px;
            height: 50px;
            border: 1px solid #000;
            padding: 2px;
            margin-top: 5px;
            text-align: center;
        }
        .signature-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .signature-label {
            font-size: 6px;
            font-weight: bold;
            margin-top: 2px;
        }
        .footer {
            margin-top: 10px;
            padding-top: 3px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 6.5px;
            color: #666;
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
                <img src="{{ $logoBase64 }}" alt="Logo" style="border: 2px solid #1e40af; border-radius: 8px; padding: 3px; background: #fff;">
                @else
                <div style="width: 70px; height: 70px; border: 2px solid #1e40af; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 8px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: #fff; font-weight: bold; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
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
                <div class="contact-info">
                    <span class="phone">Phone: 1800 XXX XXXX</span>
                    <span class="website">Website: {{ $student->institute->domain ?? 'www.mjpitm.in / www.mjpips.in' }}</span>
                    <span class="email">Email: info@{{ $student->institute->domain ?? 'mjpitm.in' }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Title -->
    <div class="main-title">Provisional Registration Form</div>

    <!-- Course Details Box -->
    <div class="course-box">
        <table>
            <tr>
                <td class="course-label">SESSION:</td>
                <td>{{ $student->session ?? $student->admission_year ?? 'N/A' }}</td>
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
        <p>
            I hereby declare that the entries made and documents submitted by me in this registration form are true to the best of my knowledge and belief. I understand that if any information provided by me is found to be false or incorrect, my registration shall be automatically cancelled and all fees paid by me shall be forfeited. The institute/university reserves the right to take any other action as deemed fit.
        </p>
        <p>
            I further declare that my registration is subject to the rules and regulations of the institute/university. I agree to abide by all the discipline and conduct rules of the institute/university. I am aware that ragging is banned and if I am found guilty of any form of ragging, I shall be liable to be punished as per the law.
        </p>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div style="font-size: 7px; font-weight: bold; margin-bottom: 3px;">Signature of the Candidate:</div>
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

    <!-- Footer -->
    <div class="footer">
        <p>Registration No: <strong>{{ $student->registration_number ?? 'N/A' }}</strong> | Generated on {{ now()->format('d M Y, h:i A') }}</p>
        <p>{{ $student->institute->name ?? 'Mahatma Jyotiba Phule Institutes' }}</p>
    </div>
</body>
</html>
