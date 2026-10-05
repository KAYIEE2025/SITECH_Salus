<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'year_level_id', 'is_active', 'adviser_id'];

    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }
}
