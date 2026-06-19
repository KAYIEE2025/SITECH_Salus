<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingComponent extends Model
{
    protected $fillable = [
        'class_schedule_id', 'grade_import_id', 'name', 'weight', 'order',
    ];

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function gradeImport()
    {
        return $this->belongsTo(GradeImport::class);
    }

    public function scoreItems()
    {
        return $this->hasMany(ScoreItem::class);
    }
}