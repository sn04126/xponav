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
     * Get all available plans
     */
    public function getPlans()
    {
        $plans = Plan::where('status', 'active')->get();

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    /**
     * Get current user's subscription status
     */
    public function getStatus(Request $request)
    {
        $user = $request->user();
        $activeSubscription = Subscription::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'has_active_subscription' => $activeSubscription !== null,
            'subscription' => $activeSubscription ? [
                'id' => $activeSubscription->id,
                'status' => $activeSubscription->status,
                'start_date' => $activeSubscription->start_date->toIso8601String(),
                'end_date' => $activeSubscription->end_date->toIso8601String(),
                'plan_name' => $activeSubscription->plan->name ?? 'Unknown',
                'plan' => $activeSubscription->plan,
            ] : null,
        ]);
    }

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

        // Check if user already has an active subscription
        $existingActive = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        if ($existingActive) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active subscription.',
                'subscription' => $existingActive,
            ], 400);
        }

        // Check if payment method belongs to user
        $paymentMethod = PaymentMethod::where('id', $request->payment_method_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $plan = Plan::findOrFail($request->plan_id);
        $code = rand(100000, 999999);

        // Calculate end date based on plan name
        $endDate = $this->calculateEndDate($plan->name);

        // Create pending subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
            'payment_method_id' => $request->payment_method_id,
            'status' => 'pending_verification',
            'start_date' => now(),
            'end_date' => $endDate,
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        // Log verification code for demo/testing
        $logFile = storage_path('logs/verification_codes.txt');
        $timestamp = now()->format('Y-m-d H:i:s');
        $content = str_repeat('=', 60) . "\n";
        $content .= "[$timestamp] Subscription Verification\n";
        $content .= str_repeat('=', 60) . "\n";
        $content .= "Email: {$user->email}\n";
        $content .= "Plan: {$plan->name} (${$plan->total_fee})\n";
        $content .= "Verification Code: $code\n";
        $content .= str_repeat('-', 60) . "\n\n";
        file_put_contents($logFile, $content, FILE_APPEND);

        // Send verification email
        Mail::to($user->email)->send(new SubscriptionVerificationMail($code));

        return response()->json([
            'success' => true,
            'message' => 'Subscription initiated. Please check your email for verification code.',
            'subscription_id' => $subscription->id,
        ]);
    }

    /**
     * Verify subscription and process payment
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
            return response()->json(['success' => false, 'message' => 'Subscription is not pending verification.'], 400);
        }

        if ($subscription->verification_code !== $request->code) {
            return response()->json(['success' => false, 'message' => 'Invalid verification code.'], 400);
        }

        if (now()->greaterThan($subscription->verification_code_expires_at)) {
            return response()->json(['success' => false, 'message' => 'Verification code expired.'], 400);
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
                'success' => false,
                'message' => 'Payment processing failed.',
                'error' => $paymentResult['error'] ?? 'Unknown error',
            ], 402);
        }

        // Calculate end date based on plan
        $endDate = $this->calculateEndDate($plan->name);

        // Activate subscription
        $subscription->update([
            'status' => 'active',
            'transaction_id' => $paymentResult['transaction_id'],
            'verification_code' => null,
            'verification_code_expires_at' => null,
            'start_date' => now(),
            'end_date' => $endDate,
        ]);

        // Update user membership
        $request->user()->update([
            'role' => 'subscriber',
            'membership_tier' => strtolower($plan->name),
            'membership_expiry' => $endDate,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription activated successfully.',
            'subscription' => [
                'id' => $subscription->id,
                'status' => 'active',
                'start_date' => $subscription->start_date->toIso8601String(),
                'end_date' => $endDate->toIso8601String(),
                'plan_name' => $plan->name,
            ],
            'payment' => [
                'transaction_id' => $paymentResult['transaction_id'],
                'amount' => $paymentResult['amount'],
                'provider' => $paymentResult['provider'],
            ],
        ]);
    }

    /**
     * Cancel active subscription
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->latest()
            ->first();

        if (!$activeSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found.',
            ], 404);
        }

        $activeSubscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Update user
        $user->update([
            'role' => 'user',
            'membership_tier' => null,
            'membership_expiry' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled. You can still use premium features until ' . $activeSubscription->end_date->format('M d, Y') . '.',
        ]);
    }

    /**
     * Calculate subscription end date based on plan name
     */
    private function calculateEndDate($planName)
    {
        return match (strtolower($planName)) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'yearly' => now()->addYear(),
            default => now()->addMonth(),
        };
    }
}
