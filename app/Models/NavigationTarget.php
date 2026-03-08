<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationTarget extends Model
{
    protected $fillable = [
        'exhibit_id',
        'floor_plan_id',
        'name',
        'position_x',
        'position_y',
        'position_z',
        'rotation_y',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position_x' => 'float',
        'position_y' => 'float',
        'position_z' => 'float',
        'rotation_y' => 'float',
    ];

    public function exhibit()
    {
        return $this->belongsTo(Exhibit::class);
    }
}
