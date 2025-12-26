<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Semester Result - {{ $semesterResult->student->name }}</title>
    <style>
        @page {
            margin: 10mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .institute-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }
        .student-info {
            margin: 15px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 3px 10px;
        }
        .student-info td:first-child {
            font-weight: bold;
            width: 30%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #000;
        }
        .signature {
            display: inline-block;
            width: 45%;
            margin-top: 40px;
        }
        .overall {
            background-color: #f0f0f0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="institute-name">{{ $semesterResult->student->institute->name ?? 'INSTITUTE NAME' }}</div>
        <div class="title">SEMESTER {{ $semesterResult->semester }} RESULT</div>
        <div style="font-size: 11px; margin-top: 5px;">Academic Year: {{ $semesterResult->academic_year }}</div>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td>Student Name:</td>
                <td>{{ $semesterResult->student->name }}</td>
                <td>Roll Number:</td>
                <td>{{ $semesterResult->student->roll_number ?? $semesterResult->student->registration_number }}</td>
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

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">S.No.</th>
                <th style="width: 35%;">Subject Name</th>
                <th style="width: 12%;">Subject Code</th>
                <th style="width: 12%;">Theory</th>
                <th style="width: 12%;">Practical</th>
                <th style="width: 12%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semesterResult->results as $index => $result)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $result->subject->name }}</td>
                    <td class="text-center">{{ $result->subject->code }}</td>
                    <td class="text-center">{{ $result->theory_marks_obtained ?? 0 }} / {{ $result->subject->theory_marks ?? 0 }}</td>
                    <td class="text-center">{{ $result->practical_marks_obtained ?? 0 }} / {{ $result->subject->practical_marks ?? 0 }}</td>
                    <td class="text-center">{{ $result->marks_obtained ?? 0 }} / {{ $result->total_marks ?? 0 }}</td>
                </tr>
            @endforeach
            <tr class="overall">
                <td colspan="5" class="text-right"><strong>OVERALL:</strong></td>
                <td class="text-center">
                    <strong>{{ $semesterResult->total_marks_obtained }} / {{ $semesterResult->total_max_marks }}</strong>
                    <div style="margin-top: 5px;">
                        <strong>{{ number_format($semesterResult->overall_percentage ?? 0, 2) }}%</strong>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div style="margin-top: 20px;">
            <div class="signature">
                <div style="border-top: 1px solid #000; width: 80%; margin-top: 40px;"></div>
                <div style="margin-top: 5px;">Controller of Examinations</div>
            </div>
            <div class="signature" style="float: right;">
                <div style="border-top: 1px solid #000; width: 80%; margin-top: 40px;"></div>
                <div style="margin-top: 5px;">Principal</div>
            </div>
        </div>
    </div>
</body>
</html>

