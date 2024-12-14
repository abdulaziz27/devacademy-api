<?php

namespace App\Http\Requests\Submission;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules()
    {
        return [
            'content' => 'required|string',
            'file_url' => 'nullable|file|mimes:pdf,jpeg,png,docx|max:10240' // 10MB max
        ];
    }
}
