<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Http\Resources\CertificateResource;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = auth()->user()->certificates()
            ->with('course')
            ->latest()
            ->get()
            ->unique('course_id')
            ->values();

        return CertificateResource::collection($certificates);
    }

    public function download(Certificate $certificate)
    {
        // Check if user owns this certificate
        if ($certificate->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Generate PDF if it doesn't exist
        if (!Storage::disk('public')->exists($certificate->pdf_path)) {
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Allow remote content like images
            $options->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($options);

            // Load the certificate view
            $html = View::make('certificates.certificate', compact('certificate'))->render();

            // Configure paper size and orientation (landscape)
            $dompdf->setPaper('A4', 'landscape');

            // Render the HTML to PDF
            $dompdf->loadHtml($html);
            $dompdf->render();

            // Save the PDF to storage
            $filePath = "certificates/certificate-{$certificate->certificate_number}.pdf";
            Storage::disk('public')->put($filePath, $dompdf->output());

            // Update the certificate path in the database
            $certificate->update(['pdf_path' => $filePath]);
        }

        // Download the PDF
        return Storage::disk('public')->download(
            $certificate->pdf_path,
            "certificate-{$certificate->certificate_number}.pdf"
        );
    }
}
