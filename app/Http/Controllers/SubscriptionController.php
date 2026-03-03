<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Plan;
use App\Models\PaymentMethod;
use App\Mail\SubscriptionVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    /**
     * Initiate a subscription
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $user = $request->user();
        
        // Check if payment method belongs to user
        $paymentMethod = PaymentMethod::where('id', $request->payment_method_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $code = rand(100000, 999999);

        // Create pending subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
            'payment_method_id' => $request->payment_method_id,
            'status' => 'pending_verification',
            'start_date' => now(),
            'end_date' => now()->addMonth(), // Default to 1 month, adjust based on plan
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        // Send verification email
        Mail::to($user->email)->send(new SubscriptionVerificationMail($code));

        return response()->json([
            'message' => 'Subscription initiated. Please check your email for verification code.',
            'subscription_id' => $subscription->id,
        ]);
    }

    /**
     * Verify subscription
     */
    public function verify(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'code' => 'required|string',
        ]);

        $subscription = Subscription::with(['plan', 'paymentMethod'])
            ->where('id', $request->subscription_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($subscription->status !== 'pending_verification') {
            return response()->json(['message' => 'Subscription is not pending verification.'], 400);
        }

        if ($subscription->verification_code !== $request->code) {
            return response()->json(['message' => 'Invalid verification code.'], 400);
        }

        if (now()->greaterThan($subscription->verification_code_expires_at)) {
            return response()->json(['message' => 'Verification code expired.'], 400);
        }

        // Process payment using PaymentService
        $paymentService = new \App\Services\PaymentService();
        $plan = $subscription->plan;
        $paymentMethod = $subscription->paymentMethod;

        $paymentResult = $paymentService->processPayment(
            $paymentMethod,
            $plan->total_fee,
            "Subscription to {$plan->name}"
        );

        if (!$paymentResult['success']) {
            return response()->json([
                'message' => 'Payment processing failed.',
                'error' => $paymentResult['error'] ?? 'Unknown error',
            ], 402); // 402 Payment Required
        }

        // Activate subscription
        $subscription->update([
            'status' => 'active',
            'transaction_id' => $paymentResult['transaction_id'],
            'verification_code' => null,
            'verification_code_expires_at' => null,
            'start_date' => now(),
            'end_date' => now()->addMonth(), // Or based on plan duration
        ]);

        // Update user role/status if needed
        $request->user()->update(['role' => 'subscriber']);

        return response()->json([
            'message' => 'Subscription activated successfully.',
            'subscription' => $subscription,
            'payment' => [
                'transaction_id' => $paymentResult['transaction_id'],
                'amount' => $paymentResult['amount'],
                'provider' => $paymentResult['provider'],
            ],
        ]);
    }
}
