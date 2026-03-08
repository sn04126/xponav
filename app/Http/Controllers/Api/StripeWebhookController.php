<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events
     * Endpoint: POST /api/stripe/webhook
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        // Verify webhook signature if secret is configured
        if ($endpointSecret) {
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $endpointSecret
                );
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                \Log::warning('Stripe webhook signature verification failed', [
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        } else {
            // In development, parse without verification
            $event = json_decode($payload);
            \Log::info('Stripe webhook received (no signature verification)', [
                'type' => $event->type ?? 'unknown',
            ]);
        }

        // Handle event types
        switch ($event->type ?? '') {
            case 'payment_intent.succeeded':
                $this->handlePaymentSuccess($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailure($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleRefund($event->data->object);
                break;

            default:
                \Log::info('Unhandled Stripe webhook event: ' . ($event->type ?? 'unknown'));
                break;
        }

        return response()->json(['received' => true]);
    }

    private function handlePaymentSuccess($paymentIntent)
    {
        $transactionId = $paymentIntent->id;

        \Log::info('Stripe payment succeeded', ['transaction_id' => $transactionId]);

        // Find subscription by transaction ID and activate if pending
        $subscription = Subscription::where('transaction_id', $transactionId)
            ->where('status', 'pending_payment')
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'active']);

            // Update user membership
            $user = $subscription->user;
            if ($user) {
                $user->update([
                    'role' => 'subscriber',
                    'membership_tier' => strtolower($subscription->plan->name ?? 'monthly'),
                    'membership_expiry' => $subscription->end_date,
                ]);
            }

            \Log::info('Subscription activated via webhook', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);
        }
    }

    private function handlePaymentFailure($paymentIntent)
    {
        $transactionId = $paymentIntent->id;

        \Log::warning('Stripe payment failed', ['transaction_id' => $transactionId]);

        $subscription = Subscription::where('transaction_id', $transactionId)->first();

        if ($subscription) {
            $subscription->update(['status' => 'payment_failed']);
        }
    }

    private function handleRefund($charge)
    {
        \Log::info('Stripe refund received', ['charge_id' => $charge->id]);

        // Find subscription and mark as refunded
        $subscription = Subscription::where('transaction_id', $charge->payment_intent)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'refunded',
                'cancelled_at' => now(),
            ]);

            $user = $subscription->user;
            if ($user) {
                $user->update([
                    'role' => 'user',
                    'membership_tier' => null,
                    'membership_expiry' => null,
                ]);
            }
        }
    }
}
