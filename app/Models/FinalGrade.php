<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalGrade extends Model
{
    protected $fillable = [
        'student_id', 'class_schedule_id', 'final_grade', 'remarks',
        'computed_at', 'status', 'submitted_at', 'reviewed_by',
        'reviewed_at', 'rejection_reason',
    ];

    protected $casts = [
        'computed_at'  => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}