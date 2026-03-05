<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Check if user has the required role
        if (!in_array(auth()->user()->role, $roles)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
