<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = Activity::with('causer')
            ->latest()
            ->paginate(50);
        
        return view('admin.activity-logs.index', compact('logs'));
    }
}
