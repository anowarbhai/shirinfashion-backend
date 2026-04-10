<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect('/admin/login');
        }

        // Check if user is admin OR has any admin role
        if (!$request->user()->is_admin && !$request->user()->roles()->exists()) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
