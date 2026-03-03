<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreARAnchorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'floor_plan_id' => 'required|exists:exhibit_floor_plans,id',
            'exhibit_id' => 'nullable|exists:exhibits,id',
            'anchor_name' => 'required|string|max:255',
            'anchor_type' => 'required|in:reference_point,exhibit_location,navigation_point,entrance,exit',
            'description' => 'nullable|string',
            
            // Position
            'position_x' => 'required|numeric',
            'position_y' => 'required|numeric',
            'position_z' => 'required|numeric',
            
            // Rotation (quaternion)
            'rotation_x' => 'nullable|numeric|between:-1,1',
            'rotation_y' => 'nullable|numeric|between:-1,1',
            'rotation_z' => 'nullable|numeric|between:-1,1',
            'rotation_w' => 'nullable|numeric|between:-1,1',
            
            // Rotation (euler angles)
            'euler_x' => 'nullable|numeric|between:-180,180',
            'euler_y' => 'nullable|numeric|between:-180,180',
            'euler_z' => 'nullable|numeric|between:-180,180',
            
            // ARKit data
            'ar_anchor_identifier' => 'nullable|string',
            'ar_world_map_data' => 'nullable|array',
            
            // Marker
            'marker_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'marker_type' => 'nullable|in:qr_code,image,nfc',
            
            // Metadata
            'metadata' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'priority' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'floor_plan_id.required' => 'The floor plan is required.',
            'floor_plan_id.exists' => 'The selected floor plan does not exist.',
            'anchor_name.required' => 'The anchor name is required.',
            'anchor_type.required' => 'The anchor type is required.',
            'anchor_type.in' => 'The anchor type must be one of: reference_point, exhibit_location, navigation_point, entrance, exit.',
            'position_x.required' => 'The X position is required.',
            'position_y.required' => 'The Y position is required.',
            'position_z.required' => 'The Z position is required.',
        ];
    }
}
