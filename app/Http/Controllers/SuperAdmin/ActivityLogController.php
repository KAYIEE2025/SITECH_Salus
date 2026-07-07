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

    public function generatePdf(Request $request)
    {
        $query = Activity::with('causer');

        // Apply date filters
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date('date_from')->startOfDay());
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date('date_to')->endOfDay());
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->whereHas('causer', function ($q) use ($request) {
                $q->whereHas('roles', function ($roleQuery) use ($request) {
                    $roleQuery->where('name', $request->string('role')->toString());
                });
            });
        }

        // Apply event filter
        if ($request->filled('event')) {
            $query->where('event', $request->string('event')->toString());
        }

        $logs = $query->latest()->get();

        $currentUser = auth()->user();
        $generatedAt = now();

        // Prepare filter information for display
        $filters = [
            'date_from' => $request->filled('date_from') ? $request->date('date_from')->format('F d, Y') : null,
            'date_to' => $request->filled('date_to') ? $request->date('date_to')->format('F d, Y') : null,
            'role' => $request->filled('role') ? $request->string('role')->toString() : null,
            'event' => $request->filled('event') ? $request->string('event')->toString() : null,
        ];

        // Generate filename based on date range
        $dateFrom = $request->filled('date_from') ? $request->date('date_from')->format('Y-m-d') : now()->format('Y-m-d');
        $dateTo = $request->filled('date_to') ? $request->date('date_to')->format('Y-m-d') : $dateFrom;
        
        if ($dateFrom === $dateTo) {
            $filename = "Activity_Logs_{$dateFrom}.pdf";
        } else {
            $filename = "Activity_Logs_{$dateFrom}_to_{$dateTo}.pdf";
        }

        // Determine action (download or print)
        $action = $request->query('action', 'download');

        // Return HTML view - browser will handle PDF generation via print dialog
        return view('superadmin.pdf.activity-logs', compact('logs', 'currentUser', 'generatedAt', 'filters', 'action', 'filename'));
    }
}
