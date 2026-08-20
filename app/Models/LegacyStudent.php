<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyStudent extends Model
{
    protected $fillable = [
        'student_number',
        'full_name',
        'first_name',
        'middle_name',
        'last_name',
        'middle_initial',
        'is_parsed',
        'parse_notes',
    ];

    /**
     * Find a legacy student by student number
     * 
     * @param string $studentNumber
     * @return static|null
     */
    public static function findByStudentNumber(string $studentNumber): ?self
    {
        return static::where('student_number', $studentNumber)->first();
    }

    /**
     * Scope to find only successfully parsed records
     */
    public function scopeParsed($query)
    {
        return $query->where('is_parsed', true);
    }

    /**
     * Scope to find records with parsing issues
     */
    public function scopeUnparsed($query)
    {
        return $query->where('is_parsed', false);
    }
}
