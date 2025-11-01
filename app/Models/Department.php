<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * Get the designations for the department.
     */
    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    /**
     * Get active designations for the department.
     */
    public function activeDesignations()
    {
        return $this->hasMany(Designation::class)->where('status', 'active');
    }
}

