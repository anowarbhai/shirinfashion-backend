<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!$request->user()) {
            return redirect('/admin/login');
        }

        // Check if user is super admin
        if ($request->user()->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has the permission
        if (!$request->user()->hasPermission($permission)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized. You do not have permission to perform this action.'], 403);
            }
            abort(403, 'Unauthorized. You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
