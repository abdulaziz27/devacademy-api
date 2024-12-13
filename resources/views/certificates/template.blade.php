<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .certificate {
            width: 100%;
            max-width: 800px;
            /* Kurangi max-width sedikit */
            margin: 0 auto;
            padding: 40px;
            text-align: center;
            border: 20px solid #787878;
            box-sizing: border-box;
            /* Pastikan padding dihitung dengan border */
        }

        .certificate-header {
            color: #787878;
            margin-bottom: 50px;
        }

        .certificate-title {
            text-align: center;
            font-size: 50px;
            margin: 20px;
        }

        .certificate-body {
            text-align: center;
        }

        .student-name {
            font-size: 30px;
            font-weight: bold;
            margin: 20px 0;
        }

        .certificate-content {
            text-align: center;
            margin: 20px 0;
        }

        .certificate-footer {
            margin-top: 50px;
            text-align: center;
        }

        @page {
            size: A4 landscape;
            margin: 20mm;
        }
    </style>
</head>

<body>
    <div class="certificate">
        <div class="certificate-header">
            <h1 class="certificate-title">Certificate of Completion</h1>
        </div>

        <div class="certificate-body">
            <p>This certifies that</p>
            <p class="student-name">{{ $user->name }}</p>
            <p>has successfully completed the course</p>
            <p class="course-name">{{ $course->title }}</p>

            <div class="certificate-content">
                <p>Completed on {{ $certificate->completion_date->format('F d, Y') }}</p>
                <p>Certificate Number: {{ $certificate->certificate_number }}</p>
            </div>
        </div>

        <div class="certificate-footer">
            <p>DevAcademy</p>
        </div>
    </div>
</body>

</html>
