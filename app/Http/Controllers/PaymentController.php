<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/payment/checkout
    // Creates a Stripe Checkout Session and returns the URL to Unity
    // ─────────────────────────────────────────────────────────────────────────
    public function checkout(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);

        $user = $request->user();
        $plan = Plan::findOrFail($request->plan_id);

        // Block if already subscribed
        $existing = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active ' . ucfirst($existing->plan->name ?? '') . ' subscription.',
            ], 400);
        }

        // Create Stripe Checkout Session (price built dynamically — no pre-created products needed)
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'unit_amount'  => (int) round($plan->total_fee * 100), // dollars → cents
                    'product_data' => [
                        'name'        => 'XpoNav ' . $plan->name . ' Plan',
                        'description' => 'AR Navigation access — ' . $plan->name . ' subscription',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode'          => 'payment',
            'success_url'   => config('app.url') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'    => config('app.url') . '/payment/cancel',
            'customer_email' => $user->email,
            'metadata'      => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
        ]);

        // Create a pending subscription so we can activate it on webhook
        Subscription::create([
            'user_id'           => $user->id,
            'plan_id'           => $plan->id,
            'status'            => 'pending',
            'start_date'        => now(),
            'end_date'          => $this->calculateEndDate($plan->name),
            'stripe_session_id' => $session->id,
        ]);

        return response()->json([
            'success'      => true,
            'checkout_url' => $session->url,
            'session_id'   => $session->id,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/payment/webhook   (no auth — Stripe calls this directly)
    // Handles checkout.session.completed event
    // ─────────────────────────────────────────────────────────────────────────
    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            \Log::warning('[Stripe Webhook] Signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $this->activateSubscription($event->data->object);
        }

        return response()->json(['received' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /payment/success   (web — opened in device browser by Stripe)
    // ─────────────────────────────────────────────────────────────────────────
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        // Backup activation: if webhook hasn't fired yet, verify & activate here
        if ($sessionId) {
            try {
                $stripeSession = StripeSession::retrieve($sessionId);
                if ($stripeSession->payment_status === 'paid') {
                    $this->activateSubscription($stripeSession);
                }
            } catch (\Exception $e) {
                \Log::warning('[Payment Success] Could not retrieve session: ' . $e->getMessage());
            }
        }

        return view('payment.success');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /payment/cancel   (web)
    // ─────────────────────────────────────────────────────────────────────────
    public function cancel()
    {
        return view('payment.cancel');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal: activate subscription from a completed Stripe session
    // ─────────────────────────────────────────────────────────────────────────
    private function activateSubscription($stripeSession): void
    {
        $subscription = Subscription::where('stripe_session_id', $stripeSession->id)->first();

        if (!$subscription || $subscription->status === 'active') {
            return; // already activated (webhook + success_url can both fire)
        }

        $plan    = $subscription->plan;
        $endDate = $this->calculateEndDate($plan->name);

        $subscription->update([
            'status'                    => 'active',
            'start_date'               => now(),
            'end_date'                 => $endDate,
            'stripe_payment_intent_id' => $stripeSession->payment_intent ?? null,
            'verification_code'        => null,
            'verification_code_expires_at' => null,
        ]);

        // Update user membership tier so /api/me and /api/subscriptions/status reflect it
        $subscription->user->update([
            'role'              => 'subscriber',
            'membership_tier'   => strtolower($plan->name),
            'membership_expiry' => $endDate,
        ]);

        \Log::info('[Stripe] Subscription activated — user_id=' . $subscription->user_id
            . ' plan=' . $plan->name
            . ' session=' . $stripeSession->id);
    }

    private function calculateEndDate(string $planName)
    {
        return match (strtolower($planName)) {
            'daily'   => now()->addDay(),
            'weekly'  => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'yearly'  => now()->addYear(),
            default   => now()->addMonth(),
        };
    }
}
