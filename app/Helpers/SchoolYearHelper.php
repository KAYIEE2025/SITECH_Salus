<?php

namespace App\Helpers;

use Carbon\Carbon;

class SchoolYearHelper
{
    /**
     * Get the active school year based on current date.
     * School year typically starts in June.
     * If current month is June or later, school year is current year to next year.
     * Otherwise, it's previous year to current year.
     *
     * @return string
     */
    public static function getActiveSchoolYear()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        
        // School year typically starts in June
        // If current month is June or later, school year is current year to next year
        // Otherwise, it's previous year to current year
        if ($currentMonth >= 6) {
            return $currentYear . '-' . ($currentYear + 1);
        } else {
            return ($currentYear - 1) . '-' . $currentYear;
        }
    }
}
