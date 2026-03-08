@extends('admin.layout')

@section('title', 'Create Plan')

@section('content')
<h2 style="color: #1D5C3C; margin-bottom: 20px;">Create Subscription Plan</h2>

<div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px; max-width: 600px;">
    <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Plan Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                   placeholder="e.g. Daily, Weekly, Monthly">
            @error('name') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Total Fee (Rs.) *</label>
                <input type="number" name="total_fee" value="{{ old('total_fee') }}" required step="0.01" min="0"
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                @error('total_fee') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Daily Fee (Rs.) *</label>
                <input type="number" name="daily_fee" value="{{ old('daily_fee') }}" required step="0.01" min="0"
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                @error('daily_fee') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Features (one per line)</label>
            <textarea name="features" rows="5"
                      style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: vertical;"
                      placeholder="Full AR Navigation&#10;All Exhibition Areas&#10;QR Code Scanning">{{ old('features') }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Status *</label>
            <select name="status" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="flex: 1; padding: 12px; background: #1D5C3C; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 600;">Create Plan</button>
            <a href="{{ route('admin.plans.index') }}" style="flex: 1; text-align: center; padding: 12px; background: #6b7280; color: white; border-radius: 8px; text-decoration: none; font-size: 15px;">Cancel</a>
        </div>
    </form>
</div>
@endsection
