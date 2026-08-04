<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get the active school year
     */
    public static function getActive()
    {
        return static::active()->first();
    }

    /**
     * Get the active school year name
     */
    public static function getActiveName()
    {
        $active = static::getActive();
        return $active ? $active->name : null;
    }
}
