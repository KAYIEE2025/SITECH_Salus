<?php

namespace App\Http\Controllers\SSG;

use App\Http\Controllers\ProfileController as BaseProfileController;

class ProfileController extends BaseProfileController
{
    public function index()
    {
        return view('ssg.profile.index');
    }
}
