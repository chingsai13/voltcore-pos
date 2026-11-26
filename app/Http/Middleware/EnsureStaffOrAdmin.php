<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffOrAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isStaff()) {
            // Customers trying to access admin panel are blocked
            abort(403, 'Unauthorized: Access restricted to staff and administrators only.');
        }

        return $next($request);
    }
}
