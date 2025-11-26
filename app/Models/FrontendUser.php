<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class FrontendUser extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'frontend_users';

    protected $fillable = [
        'username',
        'name',
        'password',
        'email',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get user's display name (username)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->username;
    }

    /**
     * Get the cities assigned to this frontend user
     */
    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'frontend_user_locations', 'frontend_user_id', 'city_id')
            ->withPivot('sector_id')
            ->withTimestamps();
    }

    /**
     * Get the sectors assigned to this frontend user
     */
    public function sectors(): BelongsToMany
    {
        return $this->belongsToMany(Sector::class, 'frontend_user_locations', 'frontend_user_id', 'sector_id')
            ->withPivot('city_id')
            ->withTimestamps();
    }

    /**
     * Get all assigned locations (cities and sectors)
     */
    public function locations()
    {
        return $this->hasMany(\App\Models\FrontendUserLocation::class, 'frontend_user_id');
    }
}

