<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InteractiveSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isFavorite = false;
        
        if ($user) {
            $isFavorite = $this->users()
                ->where('user_id', $user->id)
                ->wherePivot('is_favorite', true)
                ->exists();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date->format('Y-m-d'),
            'time' => $this->time,
            'location' => $this->location,
            'type' => $this->type,
            'hosted_by' => $this->hosted_by,
            'role' => $this->role,
            'description' => $this->description,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'is_favorite' => $isFavorite,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
