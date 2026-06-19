<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    protected $fillable = [
        'subject_id', 'teacher_id', 'section_id', 'room',
        'days', 'time_start', 'time_end', 'semester', 'school_year', 'is_active',
    ];

    protected $casts = [
        'days' => 'array',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function studyLoads()
    {
        return $this->hasMany(StudyLoad::class);
    }

    public function gradingComponents()
    {
        return $this->hasMany(GradingComponent::class);
    }

    public function finalGrades()
    {
        return $this->hasMany(FinalGrade::class);
    }

    public function gradeImports()
    {
        return $this->hasMany(GradeImport::class);
    }
}