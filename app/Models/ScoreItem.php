<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreItem extends Model
{
    protected $fillable = [
        'grading_component_id', 'name', 'max_score', 'order',
    ];

    public function gradingComponent()
    {
        return $this->belongsTo(GradingComponent::class);
    }

    public function studentScores()
    {
        return $this->hasMany(StudentScore::class);
    }
}