<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'plan'])->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }

        $subscriptions = $query->paginate(20);

        // Stats — qualify table name to avoid ambiguity with joins
        $stats = [
            'active' => Subscription::where('subscriptions.status', 'active')->where('end_date', '>', now())->count(),
            'revenue' => Subscription::where('subscriptions.status', 'active')
                ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->sum('plans.total_fee'),
            'this_month' => Subscription::where('subscriptions.status', 'active')
                ->whereMonth('subscriptions.created_at', now()->month)->count(),
            'cancelled' => Subscription::where('subscriptions.status', 'cancelled')->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }
}
