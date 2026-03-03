<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExhibitFloorPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exhibit_id',
        'name',
        'description',
        'model_file_path',
        'thumbnail_path',
        'width',
        'height',
        'length',
        'floor_level',
        'origin_latitude',
        'origin_longitude',
        'origin_altitude',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'length' => 'decimal:2',
        'origin_latitude' => 'decimal:8',
        'origin_longitude' => 'decimal:8',
        'origin_altitude' => 'decimal:2',
        'floor_level' => 'integer',
    ];

    protected $appends = [
        'model_file_url',
        'thumbnail_url',
    ];

    /**
     * Get the exhibit that owns the floor plan
     */
    public function exhibit()
    {
        return $this->belongsTo(Exhibit::class);
    }

    /**
     * Get the AR anchors for the floor plan
     */
    public function arAnchors()
    {
        return $this->hasMany(ARAnchor::class, 'floor_plan_id');
    }

    /**
     * Get active AR anchors
     */
    public function activeAnchors()
    {
        return $this->hasMany(ARAnchor::class, 'floor_plan_id')->where('is_active', true);
    }

    /**
     * Get the full URL for the 3D model file
     */
    public function getModelFileUrlAttribute()
    {
        if ($this->model_file_path) {
            return url('storage/' . $this->model_file_path);
        }
        return null;
    }

    /**
     * Get the full URL for the thumbnail
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return url('storage/' . $this->thumbnail_path);
        }
        return null;
    }

    /**
     * Scope to get only active floor plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get floor plans by exhibit
     */
    public function scopeByExhibit($query, $exhibitId)
    {
        return $query->where('exhibit_id', $exhibitId);
    }

    /**
     * Scope to get floor plans by level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('floor_level', $level);
    }
}
