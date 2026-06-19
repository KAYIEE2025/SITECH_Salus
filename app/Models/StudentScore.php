<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    protected $fillable = [
        'student_id', 'score_item_id', 'score',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scoreItem()
    {
        return $this->belongsTo(ScoreItem::class);
    }
}