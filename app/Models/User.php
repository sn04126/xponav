<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'email_verified_at',
        'phone',
        'role',
        'status',
        'image',
        'password',
        'verification_code',
        'verification_code_expires_at',
        'country',
        'city',
        'address',
        'provider',
        'provider_id',
        'provider_token',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verification_code_expires_at' => 'datetime',
        ];
    }

    public function exhibits()
    {
        return $this->belongsToMany(Exhibit::class)
            ->withPivot('is_favorite', 'is_visited', 'visited_at')
            ->withTimestamps();
    }

    public function favoriteExhibits()
    {
        return $this->belongsToMany(Exhibit::class)
            ->wherePivot('is_favorite', true);
    }

    public function visitedExhibits()
    {
        return $this->belongsToMany(Exhibit::class)
            ->wherePivot('is_visited', true);
    }

    public function interactiveSessions()
    {
        return $this->belongsToMany(InteractiveSession::class)
            ->withPivot('is_favorite')
            ->withTimestamps();
    }

    public function favoriteInteractiveSessions()
    {
        return $this->belongsToMany(InteractiveSession::class)
            ->wherePivot('is_favorite', true);
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
