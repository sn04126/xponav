<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $paymentMethods = $request->user()->paymentMethods;
        return PaymentMethodResource::collection($paymentMethods);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentMethodRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        // If this is the first payment method or set as default, update others
        if ($user->paymentMethods()->count() === 0 || ($data['is_default'] ?? false)) {
            $user->paymentMethods()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $paymentMethod = $user->paymentMethods()->create($data);

        return new PaymentMethodResource($paymentMethod);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, PaymentMethod $paymentMethod)
    {
        if ($request->user()->id !== $paymentMethod->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return new PaymentMethodResource($paymentMethod);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, PaymentMethod $paymentMethod)
    {
        if ($request->user()->id !== $paymentMethod->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $paymentMethod->delete();
        return response()->json(['message' => 'Payment method deleted successfully']);
    }

    /**
     * Set a payment method as default
     */
    public function setDefault(Request $request, PaymentMethod $paymentMethod)
    {
        if ($request->user()->id !== $paymentMethod->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->user()->paymentMethods()->update(['is_default' => false]);
        
        $paymentMethod->update(['is_default' => true]);

        return response()->json(['message' => 'Default payment method updated']);
    }
}
