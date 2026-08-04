<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GradeSubmissionReopeningRequest extends Model
{
    protected $fillable = [
        'teacher_id',
        'grading_period',
        'school_year',
        'reason',
        'status',
        'requested_at',
        'reviewed_at',
        'reviewed_by',
        'temporary_deadline',
        'approved_by',
        'approved_at',
        'remarks',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'temporary_deadline' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForPeriod($query, $schoolYear, $gradingPeriod)
    {
        return $query->where('school_year', $schoolYear)
            ->where('grading_period', $gradingPeriod);
    }

    public function isPending()
    {
        return $this->status === 'Pending';
    }

    public function isApproved()
    {
        return $this->status === 'Approved';
    }

    public function isRejected()
    {
        return $this->status === 'Rejected';
    }

    public function hasTemporaryAccess()
    {
        return $this->isApproved() && 
               $this->temporary_deadline && 
               Carbon::now()->lte($this->temporary_deadline);
    }

    public function approve($approverId, $temporaryDeadline, $remarks = null)
    {
        Log::info('MODEL APPROVE METHOD CALLED', [
            'request_id' => $this->id,
            'approver_id' => $approverId,
            'temporary_deadline' => $temporaryDeadline,
            'remarks' => $remarks,
        ]);

        $this->update([
            'status' => 'Approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'temporary_deadline' => $temporaryDeadline,
            'remarks' => $remarks,
        ]);

        Log::info('MODEL APPROVE METHOD COMPLETED', [
            'request_id' => $this->id,
            'new_status' => $this->fresh()->status,
            'approved_by' => $this->fresh()->approved_by,
            'approved_at' => $this->fresh()->approved_at,
        ]);
    }

    public function reject($reviewerId)
    {
        Log::info('MODEL REJECT METHOD CALLED', [
            'request_id' => $this->id,
            'reviewer_id' => $reviewerId,
        ]);

        $this->update([
            'status' => 'Rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
        ]);

        Log::info('MODEL REJECT METHOD COMPLETED', [
            'request_id' => $this->id,
            'new_status' => $this->fresh()->status,
        ]);
    }
}
