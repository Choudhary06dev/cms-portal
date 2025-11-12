<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name',
        'status',
    ];

    /**
     * Get the city that owns the sector.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
