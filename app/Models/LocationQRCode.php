<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class LocationQRCode extends Model
{
    use HasFactory;

    protected $table = 'location_qr_codes';

    protected $fillable = [
        'code',
        'name',
        'description',
        'exhibit_id',
        'floor_plan_id',
        'position_x',
        'position_y',
        'position_z',
        'rotation_y',
        'anchor_id',
        'is_active',
        'scan_count',
    ];

    protected $casts = [
        'position_x' => 'float',
        'position_y' => 'float',
        'position_z' => 'float',
        'rotation_y' => 'float',
        'is_active' => 'boolean',
        'scan_count' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate unique code on creation
        static::creating(function ($qrCode) {
            if (empty($qrCode->code)) {
                $qrCode->code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique QR code identifier
     */
    public static function generateUniqueCode(): string
    {
        do {
            // Format: XPONAV-XXXX-XXXX (easy to read, unique)
            $code = 'XPONAV-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get the exhibit this QR code belongs to
     */
    public function exhibit()
    {
        return $this->belongsTo(Exhibit::class);
    }

    /**
     * Get the floor plan this QR code is on
     */
    public function floorPlan()
    {
        return $this->belongsTo(ExhibitFloorPlan::class, 'floor_plan_id');
    }

    /**
     * Get the associated anchor (if any)
     */
    public function anchor()
    {
        return $this->belongsTo(ARAnchor::class, 'anchor_id');
    }

    /**
     * Increment scan count
     */
    public function incrementScanCount()
    {
        $this->increment('scan_count');
    }

    /**
     * Get position as array
     */
    public function getPositionAttribute()
    {
        return [
            'x' => $this->position_x,
            'y' => $this->position_y,
            'z' => $this->position_z,
        ];
    }

    /**
     * Scope for active QR codes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full QR code data URL for scanning
     */
    public function getQRCodeUrl(): string
    {
        return config('app.url') . '/api/qr/scan/' . $this->code;
    }
}
