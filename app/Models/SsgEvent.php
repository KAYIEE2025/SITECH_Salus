<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SsgEvent extends Model
{
    protected $fillable = [
        'title', 'description', 'event_date', 'event_time',
        'event_start_time', 'event_end_time',
        'scan_start_time', 'scan_end_time',
        'venue', 'fine_amount', 'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    /**
     * Get the computed status based on scan times using Asia/Manila timezone
     */
    public function getStatusAttribute($value)
    {
        // Compute status automatically based on scan times
        $now = Carbon::now('Asia/Manila');
        $eventDate = $this->event_date;

        // If scan times are not set, default to Upcoming
        if (!$this->scan_start_time || !$this->scan_end_time) {
            return 'Upcoming';
        }

        $startDateTime = Carbon::parse($eventDate->format('Y-m-d') . ' ' . $this->scan_start_time, 'Asia/Manila');
        $endDateTime = Carbon::parse($eventDate->format('Y-m-d') . ' ' . $this->scan_end_time, 'Asia/Manila');

        if ($now->lt($startDateTime)) {
            return 'Upcoming';
        } elseif ($now->gte($startDateTime) && $now->lte($endDateTime)) {
            return 'Ongoing';
        } else {
            return 'Done';
        }
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(SsgEventAttendance::class);
    }
}