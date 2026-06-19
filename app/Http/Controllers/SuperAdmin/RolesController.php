<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    public function index()
    {
        $roles = [
            [
                'role' => 'Super Admin',
                'color' => 'bg-yellow-100 text-yellow-800',
                'permissions' => [
                    'Create, edit, and delete all user accounts',
                    'Assign and change user roles',
                    'View all system activity logs',
                    'Full access to all system features',
                ]
            ],
            [
                'role' => 'Admin',
                'color' => 'bg-blue-100 text-blue-800',
                'permissions' => [
                    'Post and manage school announcements',
                    'Manage school calendar events',
                    'Generate PDF reports',
                    'View all activity logs',
                ]
            ],
            [
                'role' => 'Registrar',
                'color' => 'bg-purple-100 text-purple-800',
                'permissions' => [
                    'Encode and manage student profiles',
                    'Assign study loads and class schedules',
                    'Approve or reject submitted grades',
                    'View registrar-related activity logs',
                ]
            ],
            [
                'role' => 'Teacher',
                'color' => 'bg-green-100 text-green-800',
                'permissions' => [
                    'Import DepEd-formatted Excel grade files',
                    'View and manage class lists',
                    'Submit final grades to Registrar',
                    'View school announcements',
                ]
            ],
            [
                'role' => 'SSG',
                'color' => 'bg-red-100 text-red-800',
                'permissions' => [
                    'Create and manage SSG events',
                    'Set fine amounts per event',
                    'Scan student QR codes for attendance',
                    'View fine records per student',
                ]
            ],
            [
                'role' => 'Student',
                'color' => 'bg-gray-100 text-gray-800',
                'permissions' => [
                    'View personal profile and QR code',
                    'View class schedule and study load',
                    'View approved grades only',
                    'View announcements and school calendar',
                    'View SSG events and fine balance',
                ]
            ],
        ];

        return view('superadmin.roles', compact('roles'));
    }
}