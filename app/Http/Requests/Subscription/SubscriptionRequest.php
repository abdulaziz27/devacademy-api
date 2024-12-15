<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_type' => ['nullable', 'string', 'in:bank_transfer,credit_card,gopay,other_valid_types'],
        ];
    }
}
