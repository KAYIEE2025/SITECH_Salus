<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSSGTeacher
{
    /**
     * Handle an incoming request.
     * Requires user to have both SSG and Teacher roles.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized.');
        }

        if (!auth()->user()->hasRole('SSG') || !auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only SSG Teachers can manage events.');
        }

        return $next($request);
    }
}
