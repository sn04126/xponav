<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExhibitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'artist_name' => $this->artist_name,
            'artist_bio' => $this->artist_bio,
            'category' => $this->category,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'ticket_price' => $this->ticket_price,
            'is_active' => $this->is_active,
            'is_promoted' => $this->is_promoted,
            'status' => $this->status,
            'image' => $this->image_url,
            'thumbnail_url' => $this->image_url,
            'rating' => $this->rating,
            'view_count' => $this->view_count,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
