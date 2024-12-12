<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'certificate_number' => $this->certificate_number,
            'completion_date' => $this->completion_date,
            'pdf_url' => $this->pdf_path ? asset('storage/' . $this->pdf_path) : null,
            'course' => new CourseResource($this->whenLoaded('course'))
        ];
    }
}
