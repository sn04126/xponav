<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Exhibit extends Model
{
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return url('storage/' . $this->image);
        }
        return null;
    }
    protected $fillable = [
        'name',
        'title',
        'artist_name',
        'artist_bio',
        'category',
        'location',
        'status',
        'image',
        'description',
        'is_promoted',
        'is_active',
        'start_date',
        'end_date',
        'view_count',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
        'ticket_price',
        'rating',
    ];

    protected $casts = [
        'is_promoted' => 'boolean',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'ticket_price' => 'decimal:2',
        'rating' => 'decimal:1',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_favorite', 'is_visited', 'visited_at')
            ->withTimestamps();
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('is_favorite', true);
    }

    public function visitedBy()
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('is_visited', true);
    }

    public function floorPlans()
    {
        return $this->hasMany(ExhibitFloorPlan::class);
    }

    public function arAnchors()
    {
        return $this->hasMany(ARAnchor::class);
    }
}
