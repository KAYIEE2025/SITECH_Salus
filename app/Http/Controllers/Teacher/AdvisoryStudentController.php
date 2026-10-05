<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;

class AdvisoryStudentController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        // Get advisory sections for the authenticated teacher
        $advisorySections = Section::where('adviser_id', $teacherId)
            ->with(['yearLevel', 'students.yearLevel'])
            ->get();

        return view('teacher.advisory-students.index', compact('advisorySections'));
    }
}
