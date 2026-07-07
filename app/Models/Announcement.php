<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'body', 'target_type', 'target_id', 'posted_by', 'is_active',
    ];

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}