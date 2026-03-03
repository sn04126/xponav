<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteractiveSession extends Model
{
    protected $fillable = [
        'name',
        'date',
        'time',
        'location',
        'type',
        'hosted_by',
        'role',
        'description',
        'image',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_favorite')
            ->withTimestamps();
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('is_favorite', true);
    }
}
