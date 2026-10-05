<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\YearLevel;
use App\Models\User;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections   = Section::with(['yearLevel', 'adviser'])->orderBy('year_level_id')->get();
        $yearLevels = YearLevel::orderBy('level')->get();
        $teachers   = User::role('Teacher')->orderBy('name')->get();

        return view('registrar.sections.index', compact('sections', 'yearLevels', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'year_level_id' => 'required|exists:year_levels,id',
            'name'          => 'required|string|max:50',
            'adviser_id'    => 'required|exists:users,id',
        ]);

        $adviser = User::findOrFail($request->adviser_id);
        if (!$adviser->hasRole('Teacher')) {
            return back()->withInput()->withErrors(['adviser_id' => 'The selected Adviser must be a Teacher.']);
        }

        $exists = Section::where('name', $request->name)
            ->where('year_level_id', $request->year_level_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This section already exists.');
        }

        $section = Section::create([
            'name'          => $request->name,
            'year_level_id' => $request->year_level_id,
            'adviser_id'    => $request->adviser_id,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($section)
            ->log('Created section: ' . $section->name . ' (Grade ' . $section->yearLevel->name . ')');

        return back()->with('success', 'Section added successfully.');
    }

    public function edit(Section $section)
    {
        $section->load(['yearLevel', 'adviser']);
        $yearLevels = YearLevel::orderBy('level')->get();
        $teachers   = User::role('Teacher')->orderBy('name')->get();

        return view('registrar.sections.edit', compact('section', 'yearLevels', 'teachers'));
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'year_level_id' => 'required|exists:year_levels,id',
            'name'          => 'required|string|max:50',
            'adviser_id'    => 'required|exists:users,id',
        ]);

        $adviser = User::findOrFail($request->adviser_id);
        if (!$adviser->hasRole('Teacher')) {
            return back()->withInput()->withErrors(['adviser_id' => 'The selected Adviser must be a Teacher.']);
        }

        $exists = Section::where('name', $request->name)
            ->where('year_level_id', $request->year_level_id)
            ->where('id', '!=', $section->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This section already exists.');
        }

        $section->update([
            'name'          => $request->name,
            'year_level_id' => $request->year_level_id,
            'adviser_id'    => $request->adviser_id,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($section)
            ->log('Updated section: ' . $section->name . ' (Grade ' . $section->yearLevel->name . ')');

        return redirect()->route('registrar.sections')->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        if ($section->students()->count() > 0) {
            return back()->with('error', 'Cannot delete — this section has enrolled students.');
        }

        // Capture information before deletion for logging
        $sectionName = $section->name;
        $yearLevelName = $section->yearLevel->name ?? 'Unknown';

        $section->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Deleted section: ' . $sectionName . ' (Grade ' . $yearLevelName . ')');

        return back()->with('success', 'Section deleted.');
    }
}
