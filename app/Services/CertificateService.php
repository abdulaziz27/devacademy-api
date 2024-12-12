<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateService
{
    public function generateCertificate(User $user, Course $course)
    {
        // Generate Certificate Number
        $certificateNumber = $this->generateCertificateNumber();

        // Create Certificate Record
        $certificate = Certificate::create([
            'certificate_number' => $certificateNumber,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'completion_date' => now()
        ]);

        // Generate PDF
        $pdf = PDF::loadView('certificates.template', [
            'certificate' => $certificate,
            'user' => $user,
            'course' => $course
        ]);

        // Save PDF
        $fileName = "certificate-{$certificateNumber}.pdf";
        $path = "certificates/{$fileName}";
        Storage::disk('public')->put($path, $pdf->output());

        // Update Certificate with PDF path
        $certificate->update(['pdf_path' => $path]);

        return $certificate;
    }

    private function generateCertificateNumber()
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (Certificate::where('certificate_number', $number)->exists());

        return $number;
    }
}
