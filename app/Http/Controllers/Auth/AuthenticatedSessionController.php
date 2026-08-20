<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        activity()
            ->event('login')
            ->causedBy($user)
            ->log('Logged in');

        if ($user->hasRole('Super Admin')) {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('Registrar')) {
            return redirect()->route('registrar.dashboard');
        } elseif ($user->hasRole('Teacher')) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->hasRole('Student')) {
            // Check if student must change password
            if ($user->must_change_password) {
                return redirect()->route('student.forced-password-change');
            }
            return redirect()->route('student.dashboard');
        } elseif ($user->hasRole('SSG')) {
            return redirect()->route('ssg.dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            activity()
                ->event('logout')
                ->causedBy($user)
                ->log('Logged out');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
