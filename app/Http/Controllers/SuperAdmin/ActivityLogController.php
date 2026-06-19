<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = Activity::with('causer')
            ->latest()
            ->paginate(20);

        return view('superadmin.activity-logs', compact('logs'));
    }
}