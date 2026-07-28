<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class GradeSubmissionSchedule extends Model
{
    use LogsActivity;

    protected $fillable = [
        'school_year',
        'grading_period',
        'start_at',
        'end_at',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusAttribute(): string
    {
        $now = now();

        if ($now->lt($this->start_at)) {
            return 'Upcoming';
        }

        if ($now->between($this->start_at, $this->end_at)) {
            return 'Open';
        }

        return 'Closed';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['school_year', 'grading_period', 'start_at', 'end_at', 'created_by'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function scopeForSchoolYear($query, $schoolYear)
    {
        return $query->where('school_year', $schoolYear);
    }

    public function scopeForGradingPeriod($query, $period)
    {
        return $query->where('grading_period', $period);
    }
}
