<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Activity::with('causer')
            ->when($request->filled('causer_id'), function ($query) use ($request) {
                $query->where('causer_id', $request->integer('causer_id'));
            })
            ->when($request->filled('event'), function ($query) use ($request) {
                $query->where('event', $request->string('event')->toString());
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('description', 'like', '%' . $request->string('search')->toString() . '%');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name']);
        $events = Activity::query()
            ->whereNotNull('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return view('superadmin.activity-logs', compact('logs', 'users', 'events'));
    }
}
