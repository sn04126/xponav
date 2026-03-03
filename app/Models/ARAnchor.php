<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ARAnchor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ar_anchors';

    protected $fillable = [
        'floor_plan_id',
        'exhibit_id',
        'anchor_name',
        'anchor_type',
        'description',
        'position_x',
        'position_y',
        'position_z',
        'rotation_x',
        'rotation_y',
        'rotation_z',
        'rotation_w',
        'euler_x',
        'euler_y',
        'euler_z',
        'ar_anchor_identifier',
        'ar_world_map_data',
        'marker_image_path',
        'marker_type',
        'metadata',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'metadata' => 'array',
        'ar_world_map_data' => 'array',
        'is_active' => 'boolean',
        'position_x' => 'decimal:4',
        'position_y' => 'decimal:4',
        'position_z' => 'decimal:4',
        'rotation_x' => 'decimal:6',
        'rotation_y' => 'decimal:6',
        'rotation_z' => 'decimal:6',
        'rotation_w' => 'decimal:6',
        'euler_x' => 'decimal:4',
        'euler_y' => 'decimal:4',
        'euler_z' => 'decimal:4',
        'priority' => 'integer',
    ];

    protected $appends = [
        'marker_image_url',
        'position',
        'rotation_quaternion',
        'rotation_euler',
    ];

    /**
     * Get the floor plan that owns the anchor
     */
    public function floorPlan()
    {
        return $this->belongsTo(ExhibitFloorPlan::class, 'floor_plan_id');
    }

    /**
     * Get the exhibit associated with the anchor
     */
    public function exhibit()
    {
        return $this->belongsTo(Exhibit::class);
    }

    /**
     * Get the full URL for the marker image
     */
    public function getMarkerImageUrlAttribute()
    {
        if ($this->marker_image_path) {
            return url('storage/' . $this->marker_image_path);
        }
        return null;
    }

    /**
     * Get position as array for ARKit
     */
    public function getPositionAttribute()
    {
        return [
            'x' => (float) $this->position_x,
            'y' => (float) $this->position_y,
            'z' => (float) $this->position_z,
        ];
    }

    /**
     * Get rotation as quaternion for ARKit
     */
    public function getRotationQuaternionAttribute()
    {
        return [
            'x' => (float) $this->rotation_x,
            'y' => (float) $this->rotation_y,
            'z' => (float) $this->rotation_z,
            'w' => (float) $this->rotation_w,
        ];
    }

    /**
     * Get rotation as euler angles
     */
    public function getRotationEulerAttribute()
    {
        return [
            'pitch' => (float) $this->euler_x,
            'yaw' => (float) $this->euler_y,
            'roll' => (float) $this->euler_z,
        ];
    }

    /**
     * Scope to get only active anchors
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get anchors by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('anchor_type', $type);
    }

    /**
     * Scope to get anchors by floor plan
     */
    public function scopeByFloorPlan($query, $floorPlanId)
    {
        return $query->where('floor_plan_id', $floorPlanId);
    }

    /**
     * Scope to order by priority
     */
    public function scopeOrderByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
