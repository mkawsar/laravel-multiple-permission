<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if ($request->user()->roles()->where('name', '=', $role)->exists()) {
            return $next($request);
        }

        return response()->json([
            'data' => 'You have not permission for this action'
        ], 403);
    }
}
