<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFloorPlanRequest extends FormRequest
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
            'exhibit_id' => 'required|exists:exhibits,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // 'model_file' => 'required|file|mimes:usdz,obj,dae,gltf,glb|max:51200', // 50MB max
            // 'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'width' => 'required|numeric|min:0.01',
            'height' => 'required|numeric|min:0.01',
            'length' => 'required|numeric|min:0.01',
            'floor_level' => 'required|integer',
            'origin_latitude' => 'nullable|numeric|between:-90,90',
            'origin_longitude' => 'nullable|numeric|between:-180,180',
            'origin_altitude' => 'nullable|numeric',
            'metadata' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'exhibit_id.required' => 'The exhibit is required.',
            'exhibit_id.exists' => 'The selected exhibit does not exist.',
            'name.required' => 'The floor plan name is required.',
            'model_file.required' => 'The 3D model file is required.',
            'model_file.mimes' => 'The 3D model must be a file of type: usdz, obj, dae, gltf, glb.',
            'width.required' => 'The floor width is required.',
            'height.required' => 'The floor height is required.',
            'length.required' => 'The floor length is required.',
            'floor_level.required' => 'The floor level is required.',
        ];
    }
}
