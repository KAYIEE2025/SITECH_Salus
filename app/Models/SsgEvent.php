<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsgEvent extends Model
{
    protected $fillable = [
        'title', 'description', 'event_date', 'event_time',
        'venue', 'fine_amount', 'status', 'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(SsgEventAttendance::class);
    }
}