<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::with('causer', 'subject')
            ->when($request->filled('search'), fn ($q) => $q->where('description', 'like', '%' . $request->search . '%'))
            ->when($request->filled('log_name'), fn ($q) => $q->where('log_name', $request->log_name))
            ->latest()
            ->paginate(20);

        return view('pages.admin.activity-logs.index', [
            'activities' => $activities,
            'totalLogs' => Activity::count(),
        ]);
    }

    public function show($id)
    {
        $activity = Activity::with('causer', 'subject')->findOrFail($id);

        return view('pages.admin.activity-logs.show', [
            'activity' => $activity,
        ]);
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return redirect()->route('admin.activity-logs.index')
            ->with('success', 'Log record deleted.');
    }

    public function destroyAll(Request $request)
    {
        Activity::truncate();

        return redirect()->route('admin.activity-logs.index')
            ->with('success', 'All activity logs have been cleared.');
    }
}
