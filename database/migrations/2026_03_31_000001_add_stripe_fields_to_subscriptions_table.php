<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Make payment_method_id nullable (Stripe flow doesn't use it)
            $table->unsignedBigInteger('payment_method_id')->nullable()->change();

            // Stripe Checkout session ID (cs_test_xxx)
            $table->string('stripe_session_id')->nullable()->after('transaction_id');

            // Stripe PaymentIntent ID (pi_xxx) — filled by webhook
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['stripe_session_id', 'stripe_payment_intent_id']);
            $table->unsignedBigInteger('payment_method_id')->nullable(false)->change();
        });
    }
};
