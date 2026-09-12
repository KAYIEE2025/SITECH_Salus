<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsgEventAttendance extends Model
{
    protected $fillable = [
        'ssg_event_id', 'student_id', 'is_present',
        'scanned_at', 'scanned_by_user_id', 'applicable_fine', 'actual_fine',
    ];

    protected $casts = [
        'is_present' => 'boolean',
        'scanned_at' => 'datetime',
    ];

    public function ssgEvent()
    {
        return $this->belongsTo(SsgEvent::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by_user_id');
    }
}
