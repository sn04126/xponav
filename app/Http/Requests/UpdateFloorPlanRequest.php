<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFloorPlanRequest extends FormRequest
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
            'exhibit_id' => 'sometimes|exists:exhibits,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'model_file' => 'sometimes|file|mimes:usdz,obj,dae,gltf,glb|max:51200', // 50MB max
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'width' => 'sometimes|numeric|min:0.01',
            'height' => 'sometimes|numeric|min:0.01',
            'length' => 'sometimes|numeric|min:0.01',
            'floor_level' => 'sometimes|integer',
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
            'exhibit_id.exists' => 'The selected exhibit does not exist.',
            'model_file.mimes' => 'The 3D model must be a file of type: usdz, obj, dae, gltf, glb.',
        ];
    }
}
