<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionTransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'payment_type' => $this->payment_type,
            'snap_token' => $this->snap_token,
            'plan' => new SubscriptionPlanResource($this->whenLoaded('plan'))
        ];
    }
}
