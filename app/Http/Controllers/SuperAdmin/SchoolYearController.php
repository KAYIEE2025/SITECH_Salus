<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::orderBy('name')->get();
        return view('superadmin.school-years.index', compact('schoolYears'));
    }

    public function create()
    {
        return view('superadmin.school-years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:school_years,name',
        ]);

        SchoolYear::create([
            'name' => $request->name,
            'is_active' => false,
        ]);

        return redirect()->route('superadmin.school-years.index')
            ->with('success', 'School year added successfully.');
    }

    public function edit(SchoolYear $schoolYear)
    {
        return view('superadmin.school-years.edit', compact('schoolYear'));
    }

    public function update(Request $request, SchoolYear $schoolYear)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:school_years,name,' . $schoolYear->id,
        ]);

        $schoolYear->update([
            'name' => $request->name,
        ]);

        return redirect()->route('superadmin.school-years.index')
            ->with('success', 'School year updated successfully.');
    }

    public function setActive(SchoolYear $schoolYear)
    {
        // Set all school years to inactive
        SchoolYear::query()->update(['is_active' => false]);

        // Set the selected school year to active
        $schoolYear->update(['is_active' => true]);

        return redirect()->route('superadmin.school-years.index')
            ->with('success', 'School year set as active successfully.');
    }

    public function destroy(SchoolYear $schoolYear)
    {
        $schoolYear->delete();

        return redirect()->route('superadmin.school-years.index')
            ->with('success', 'School year deleted successfully.');
    }
}
