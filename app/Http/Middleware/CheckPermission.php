<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect('/admin/login');
        }

        // Super admin (is_super role) bypasses all permission checks
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $routeName = $request->route()->getName();

        // Map route actions to required permissions
        $actionPermissionMap = [
            'products.index' => 'products.view',
            'products.show' => 'products.view',
            'products.create' => 'products.create',
            'products.store' => 'products.create',
            'products.edit' => 'products.edit',
            'products.update' => 'products.edit',
            'products.destroy' => 'products.delete',

            'categories.index' => 'categories.view',
            'categories.show' => 'categories.view',
            'categories.create' => 'categories.create',
            'categories.store' => 'categories.create',
            'categories.edit' => 'categories.edit',
            'categories.update' => 'categories.edit',
            'categories.destroy' => 'categories.delete',

            'orders.index' => 'orders.view',
            'orders.show' => 'orders.view',
            'orders.create' => 'orders.create',
            'orders.store' => 'orders.create',
            'orders.edit' => 'orders.edit',
            'orders.update' => 'orders.edit',
            'orders.destroy' => 'orders.delete',

            'customers.index' => 'customers.view',
            'customers.show' => 'customers.view',
            'customers.edit' => 'customers.edit',
            'customers.update' => 'customers.edit',
            'customers.destroy' => 'customers.delete',

            'coupons.index' => 'coupons.view',
            'coupons.show' => 'coupons.view',
            'coupons.create' => 'coupons.create',
            'coupons.store' => 'coupons.create',
            'coupons.edit' => 'coupons.edit',
            'coupons.update' => 'coupons.edit',
            'coupons.destroy' => 'coupons.delete',

            'sliders.index' => 'sliders.view',
            'sliders.show' => 'sliders.view',
            'sliders.create' => 'sliders.create',
            'sliders.store' => 'sliders.create',
            'sliders.edit' => 'sliders.edit',
            'sliders.update' => 'sliders.edit',
            'sliders.destroy' => 'sliders.delete',

            'pages.index' => 'pages.view',
            'pages.show' => 'pages.view',
            'pages.create' => 'pages.edit',
            'pages.store' => 'pages.edit',
            'pages.edit' => 'pages.edit',
            'pages.update' => 'pages.edit',
            'pages.destroy' => 'pages.edit',

            'users.index' => 'users.view',
            'users.show' => 'users.view',
            'users.create' => 'users.create',
            'users.store' => 'users.create',
            'users.edit' => 'users.edit',
            'users.update' => 'users.edit',
            'users.destroy' => 'users.delete',

            'roles.index' => 'roles.view',
            'roles.show' => 'roles.view',
            'roles.create' => 'roles.create',
            'roles.store' => 'roles.create',
            'roles.edit' => 'roles.edit',
            'roles.update' => 'roles.edit',
            'roles.destroy' => 'roles.delete',

            'permissions.index' => 'roles.view',
            'permissions.show' => 'roles.view',
            'permissions.create' => 'roles.create',
            'permissions.store' => 'roles.create',
            'permissions.edit' => 'roles.edit',
            'permissions.update' => 'roles.edit',
            'permissions.destroy' => 'roles.delete',

            // Special routes
            'volume-discounts.index' => 'volume-discounts.view',
            'volume-discounts' => 'volume-discounts.view',
        ];

        // Get the required permission for this specific route action
        $requiredPermission = $actionPermissionMap[$routeName] ?? $permission;

        // Check if user has the permission
        if (! $user->hasPermission($requiredPermission)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not have permission: '.$requiredPermission,
                ], 403);
            }

            return back()->with('error', 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
