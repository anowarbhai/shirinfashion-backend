<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect('/admin/login');
        }

        // Load roles with permissions for permission checks
        $user = $request->user();
        if (! $user->relationLoaded('roles')) {
            $user->load('roles.permissions');
        }

        // Check if user is admin OR has any admin role
        if (! $user->is_admin && ! $user->roles()->exists()) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
