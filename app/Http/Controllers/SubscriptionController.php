<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\SubscriptionRequest;
use App\Http\Resources\SubscriptionPlanResource;
use App\Http\Resources\SubscriptionTransactionResource;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use App\Helpers\MidtransHelper;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return SubscriptionPlanResource::collection($plans);
    }

    public function subscribe(SubscriptionRequest $request, SubscriptionPlan $plan)
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = 'ORDER-' . time();

        $transaction = SubscriptionTransaction::create([
            'user_id' => auth()->id(),
            'subscription_plan_id' => $plan->id,
            'order_id' => $orderId,
            'amount' => $plan->price,
            'status' => 'pending',
            'payment_type' => $request->payment_type
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) ($plan->price * 100)
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email
            ],
            'item_details' => [
                [
                    'id' => $plan->id,
                    'price' => (int) ($plan->price * 100),
                    'quantity' => 1,
                    'name' => $plan->name
                ]
            ]
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);

            return response()->json([
                'snap_token' => $snapToken,
                'transaction' => new SubscriptionTransactionResource($transaction)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        if (app()->environment('local')) {
            // In test mode, skip signature validation
            $transaction = SubscriptionTransaction::where('order_id', $request->order_id)->firstOrFail();

            // Auto update to settlement
            $transaction->update([
                'status' => 'settlement',
                'payment_details' => $request->all()
            ]);

            // Create active subscription
            UserSubscription::create([
                'user_id' => $transaction->user_id,
                'start_date' => now(),
                'end_date' => now()->addDays($transaction->plan->duration_in_days),
                'is_active' => true
            ]);

            return response()->json(['message' => 'Payment completed successfully']);
        }

        $serverKey = config('services.midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed != $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $transaction = SubscriptionTransaction::where('order_id', $request->order_id)->firstOrFail();
        $transaction->update([
            'status' => $request->transaction_status,
            'payment_details' => $request->all()
        ]);

        if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
            UserSubscription::create([
                'user_id' => $transaction->user_id,
                'start_date' => now(),
                'end_date' => now()->addDays($transaction->plan->duration_in_days),
                'is_active' => true
            ]);
        }

        return response()->json(['message' => 'Callback handled successfully']);
    }

    // Method for testing
    public function generateTestSignature(Request $request)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $signature = MidtransHelper::generateSignature(
            $request->order_id,
            $request->status_code,
            $request->gross_amount
        );

        return response()->json(['signature_key' => $signature]);
    }
}
