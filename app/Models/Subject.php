<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['code', 'name', 'units', 'description', 'is_active'];

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }
}