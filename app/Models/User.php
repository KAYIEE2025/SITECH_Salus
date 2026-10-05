<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'profile_photo_path', 'contact_number', 'is_active', 'must_change_password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
        'must_change_password' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function encodedStudents()
    {
        return $this->hasMany(Student::class, 'encoded_by');
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class, 'teacher_id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'posted_by');
    }

    public function schoolEvents()
    {
        return $this->hasMany(SchoolEvent::class, 'created_by');
    }

    public function ssgEvents()
    {
        return $this->hasMany(SsgEvent::class, 'created_by');
    }

    public function gradeImports()
    {
        return $this->hasMany(GradeImport::class, 'teacher_id');
    }

    public function reviewedGrades()
    {
        return $this->hasMany(FinalGrade::class, 'reviewed_by');
    }

    public function advisorySections()
    {
        return $this->hasMany(Section::class, 'adviser_id');
    }
}
