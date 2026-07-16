<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeHistory extends Model
{
    protected $fillable = [
        'final_grade_id',
        'user_id',
        'action',
        'status',
        'description',
        'grade_data',
        'rejection_reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'grade_data' => 'array',
    ];

    public function finalGrade()
    {
        return $this->belongsTo(FinalGrade::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
