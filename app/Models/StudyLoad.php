<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLoad extends Model
{
    protected $fillable = [
        'student_id', 'class_schedule_id', 'semester', 'school_year',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class);
    }
}