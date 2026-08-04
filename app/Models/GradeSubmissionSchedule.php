<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GradeSubmissionSchedule extends Model
{
    protected $fillable = ['school_year', 'grading_period', 'start_at', 'end_at', 'created_by'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function getDeadlineAtAttribute()
    {
        return $this->end_at;
    }

    public function getStatusAttribute()
    {
        $now = Carbon::now();

        if ($now < $this->start_at) {
            return 'Scheduled';
        } elseif ($now >= $this->start_at && $now <= $this->end_at) {
            return 'Open';
        } else {
            return 'Closed';
        }
    }
}
