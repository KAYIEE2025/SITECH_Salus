<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'student_type', 'student_number', 'first_name', 'middle_name', 'last_name',
        'suffix', 'date_of_birth', 'gender', 'address', 'contact_number', 'email',
        'guardian_name', 'guardian_contact', 'guardian_relationship',
        'year_level_id', 'section_id', 'school_year',
        'status', 'photo_path', 'qr_code_value', 'qr_code_path',
        'encoded_by', 'encoded_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function encoder()
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }

    public function studyLoads()
    {
        return $this->hasMany(StudyLoad::class);
    }

    public function studentScores()
    {
        return $this->hasMany(StudentScore::class);
    }

    public function finalGrades()
    {
        return $this->hasMany(FinalGrade::class);
    }

    public function ssgAttendances()
    {
        return $this->hasMany(SsgEventAttendance::class);
    }

    public function getFullNameAttribute()
    {
        return $this->last_name . ', ' . $this->first_name . ' ' . $this->middle_name;
    }
}
