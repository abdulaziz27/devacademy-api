<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Http\Resources\CertificateResource;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = auth()->user()->certificates()
            ->with('course')
            ->latest()
            ->get();

        return CertificateResource::collection($certificates);
    }

    public function download(Certificate $certificate)
    {
        // Check if user owns this certificate
        if ($certificate->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!Storage::disk('public')->exists($certificate->pdf_path)) {
            return response()->json(['message' => 'Certificate not found'], 404);
        }

        return Storage::disk('public')->download(
            $certificate->pdf_path,
            "certificate-{$certificate->certificate_number}.pdf"
        );
    }
}
