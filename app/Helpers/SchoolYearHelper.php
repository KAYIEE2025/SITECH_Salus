<?php

namespace App\Helpers;

use App\Models\SchoolYear;

class SchoolYearHelper
{
    /**
     * Get the active school year name
     */
    public static function getActive()
    {
        $active = SchoolYear::active()->first();
        return $active ? $active->name : null;
    }

    /**
     * Get the active school year model
     */
    public static function getActiveModel()
    {
        return SchoolYear::active()->first();
    }

    /**
     * Check if there is an active school year
     */
    public static function hasActive()
    {
        return SchoolYear::active()->exists();
    }
}