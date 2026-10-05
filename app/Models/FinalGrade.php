<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalGrade extends Model
{
    protected $fillable = [
        'student_id', 'class_schedule_id', 'final_grade', 'initial_grade',
        'transmuted_grade', 'quarterly_grade', 'remarks',
        'component_scores', 'computed_at', 'status', 'submitted_at', 'reviewed_by',
        'reviewed_at', 'rejection_reason',
        'written_works', 'performance_tasks', 'quarterly_assessment',
        'student_name', 'student_number', 'imported_data',
        'term_1', 'term_2', 'term_3', 'final_rating',
        'grading_period',
    ];

    protected $casts = [
        'computed_at'  => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
        'component_scores' => 'array',
        'imported_data' => 'array',
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

    public function gradeHistories()
    {
        return $this->hasMany(GradeHistory::class)->orderBy('created_at');
    }

    public function recordHistory($action, $status, $description = null, $rejectionReason = null)
    {
        return $this->gradeHistories()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'status' => $status,
            'description' => $description,
            'grade_data' => $this->toArray(),
            'rejection_reason' => $rejectionReason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}