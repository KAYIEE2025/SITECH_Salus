<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolEvent extends Model
{
    protected $fillable = [
        'title', 'description', 'event_date', 'event_end_date', 'color', 'created_by',
    ];

    protected $casts = [
        'event_date'     => 'date',
        'event_end_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}