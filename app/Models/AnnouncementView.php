<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementView extends Model
{
    protected $fillable = [
        'user_id',
        'announcement_id',
        'viewed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}
