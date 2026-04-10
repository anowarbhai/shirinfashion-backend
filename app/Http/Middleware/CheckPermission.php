<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!$request->user()) {
            return redirect('/admin/login');
        }

        // Allow if is_admin
        if ($request->user()->is_admin) {
            return $next($request);
        }

        // Check if user has the specific permission
        if (!$request->user()->hasPermission($permission)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return redirect('/admin/dashboard')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
