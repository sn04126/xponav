<?php

namespace App\Services;

class PaymentService
{
    /**
     * Process payment based on payment method type
     */
    public function processPayment($paymentMethod, $amount, $description = 'Subscription Payment')
    {
        try {
            switch ($paymentMethod->type) {
                case 'stripe':
                    return $this->processStripePayment($paymentMethod, $amount, $description);
                
                case 'paypal':
                    return $this->processPayPalPayment($paymentMethod, $amount, $description);
                
                case 'card':
                    return $this->processCardPayment($paymentMethod, $amount, $description);
                
                default:
                    throw new \Exception('Unsupported payment method type: ' . $paymentMethod->type);
            }
        } catch (\Exception $e) {
            \Log::error('Payment processing failed: ' . $e->getMessage(), [
                'payment_method_id' => $paymentMethod->id,
                'amount' => $amount,
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process Stripe payment
     */
    protected function processStripePayment($paymentMethod, $amount, $description)
    {
        try {
            // Set Stripe API key
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            
            // Create a PaymentIntent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount * 100, // Stripe uses cents
                'currency' => 'usd',
                'description' => $description,
                'payment_method' => $paymentMethod->provider_id, // Stripe payment method ID
                'confirm' => true, // Automatically confirm the payment
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);

            \Log::info('Stripe payment processed successfully', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'status' => $paymentIntent->status,
            ]);

            return [
                'success' => true,
                'transaction_id' => $paymentIntent->id,
                'amount' => $amount,
                'provider' => 'stripe',
                'status' => $paymentIntent->status,
            ];
            
        } catch (\Stripe\Exception\CardException $e) {
            // Card was declined
            \Log::error('Stripe card declined', [
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getStripeCode(),
            ];
            
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid parameters
            \Log::error('Stripe invalid request', [
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => 'Invalid payment request: ' . $e->getMessage(),
            ];
            
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe failed
            \Log::error('Stripe authentication failed', [
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => 'Payment gateway authentication failed',
            ];
            
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication failed
            \Log::error('Stripe API connection failed', [
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => 'Payment gateway connection failed',
            ];
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Generic Stripe error
            \Log::error('Stripe API error', [
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => 'Payment processing failed',
            ];
            
        } catch (\Exception $e) {
            // Something else happened
            \Log::error('Stripe payment failed', [
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => 'An unexpected error occurred',
            ];
        }
    }

    /**
     * Process PayPal payment
     */
    protected function processPayPalPayment($paymentMethod, $amount, $description)
    {
        // In production, you would use PayPal SDK:
        // $apiContext = new \PayPal\Rest\ApiContext(
        //     new \PayPal\Auth\OAuthTokenCredential(
        //         env('PAYPAL_CLIENT_ID'),
        //         env('PAYPAL_SECRET')
        //     )
        // );
        //
        // $payer = new \PayPal\Api\Payer();
        // $payer->setPaymentMethod('paypal');
        //
        // $amount = new \PayPal\Api\Amount();
        // $amount->setTotal($amount);
        // $amount->setCurrency('USD');
        //
        // $transaction = new \PayPal\Api\Transaction();
        // $transaction->setAmount($amount);
        // $transaction->setDescription($description);
        //
        // $payment = new \PayPal\Api\Payment();
        // $payment->setIntent('sale');
        // $payment->setPayer($payer);
        // $payment->setTransactions([$transaction]);
        //
        // $payment->create($apiContext);

        // Mock response for development
        \Log::info('PayPal payment processed (MOCK)', [
            'payment_method_id' => $paymentMethod->id,
            'amount' => $amount,
            'provider_id' => $paymentMethod->provider_id,
        ]);

        return [
            'success' => true,
            'transaction_id' => 'paypal_' . uniqid(),
            'amount' => $amount,
            'provider' => 'paypal',
        ];
    }

    /**
     * Process card payment (generic card processor)
     */
    protected function processCardPayment($paymentMethod, $amount, $description)
    {
        // In production, you would integrate with your card processor
        // This could be Stripe, Braintree, Authorize.net, etc.
        
        // Mock response for development
        \Log::info('Card payment processed (MOCK)', [
            'payment_method_id' => $paymentMethod->id,
            'amount' => $amount,
            'last_four' => $paymentMethod->last_four,
        ]);

        return [
            'success' => true,
            'transaction_id' => 'card_' . uniqid(),
            'amount' => $amount,
            'provider' => 'card',
        ];
    }

    /**
     * Refund a payment
     */
    public function refundPayment($transactionId, $amount)
    {
        // In production, implement refund logic based on provider
        \Log::info('Payment refund processed (MOCK)', [
            'transaction_id' => $transactionId,
            'amount' => $amount,
        ]);

        return [
            'success' => true,
            'refund_id' => 'refund_' . uniqid(),
            'amount' => $amount,
        ];
    }
}
