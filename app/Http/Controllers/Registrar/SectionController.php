<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\YearLevel;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections   = Section::with('yearLevel')->orderBy('year_level_id')->get();
        $yearLevels = YearLevel::orderBy('level')->get();

        return view('registrar.sections.index', compact('sections', 'yearLevels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'year_level_id' => 'required|exists:year_levels,id',
            'name'          => 'required|string|max:10',
        ]);

        $exists = Section::where('name', $request->name)
            ->where('year_level_id', $request->year_level_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This section already exists.');
        }

        Section::create([
            'name'          => $request->name,
            'year_level_id' => $request->year_level_id,
        ]);

        return back()->with('success', 'Section added successfully.');
    }

    public function destroy(Section $section)
    {
        if ($section->students()->count() > 0) {
            return back()->with('error', 'Cannot delete — this section has enrolled students.');
        }

        $section->delete();
        return back()->with('success', 'Section deleted.');
    }
}
