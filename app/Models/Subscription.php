<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'payment_method_id',
        'transaction_id',
        'status',
        'start_date',
        'end_date',
        'cancelled_at',
        'verification_code',
        'verification_code_expires_at',
        'stripe_session_id',
        'stripe_payment_intent_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'verification_code_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
