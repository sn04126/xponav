@extends('admin.layout')

@section('title', 'Subscription Plans')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="color: #1D5C3C; margin: 0;">Subscription Plans</h2>
    <a href="{{ route('admin.plans.create') }}" style="background: #1D5C3C; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px;">+ Add Plan</a>
</div>

@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    @foreach($plans as $plan)
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        <div style="background: {{ $plan->status === 'active' ? '#1D5C3C' : '#6b7280' }}; color: white; padding: 20px; text-align: center;">
            <h3 style="font-size: 24px; margin-bottom: 5px;">{{ $plan->name }}</h3>
            <div style="font-size: 36px; font-weight: 700;">${{ number_format($plan->total_fee) }}</div>
            <div style="font-size: 13px; opacity: 0.8;">${{ number_format($plan->daily_fee, 2) }}/day</div>
        </div>
        <div style="padding: 20px;">
            @if($plan->features)
                <ul style="list-style: none; padding: 0; margin: 0 0 16px 0;">
                    @php $features = is_array($plan->features) ? $plan->features : json_decode($plan->features, true) ?? []; @endphp
                    @foreach($features as $feature)
                        <li style="padding: 6px 0; font-size: 13px; color: #555; border-bottom: 1px solid #f0f0f0;">
                            &#10003; {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            @endif

            <div style="display: flex; gap: 8px;">
                <a href="{{ route('admin.plans.edit', $plan) }}" style="flex: 1; text-align: center; padding: 8px; background: #1D5C3C; color: white; border-radius: 6px; text-decoration: none; font-size: 13px;">Edit</a>
                <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Delete this plan?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="width: 100%; padding: 8px; background: #dc3545; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($plans->isEmpty())
    <div style="text-align: center; padding: 60px 20px; color: #666;">
        <h3>No plans yet</h3>
        <p>Create your first subscription plan.</p>
    </div>
@endif
@endsection
