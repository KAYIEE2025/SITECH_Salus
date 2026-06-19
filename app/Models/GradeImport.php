<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeImport extends Model
{
    protected $fillable = [
        'class_schedule_id', 'teacher_id', 'file_path',
        'original_filename', 'status', 'error_message', 'imported_at',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
    ];

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function gradingComponents()
    {
        return $this->hasMany(GradingComponent::class);
    }
}