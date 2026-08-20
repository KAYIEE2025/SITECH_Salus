<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentMustChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only apply to authenticated students
        if ($user && $user->hasRole('Student') && $user->must_change_password) {
            // Allow access to the forced password change page and logout
            $allowedRoutes = [
                'student.forced-password-change',
                'student.forced-password-change.store',
                'logout',
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('student.forced-password-change');
            }
        }

        return $next($request);
    }
}
