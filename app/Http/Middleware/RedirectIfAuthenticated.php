<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param string|null ...$guards
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'code' => 101, // means auth error in the api,
                    'response' => null // nothing to show
                ]);
            }
        } catch (TokenExpiredException $e) {
            // If the token is expired, then it will be refreshed and added to the headers
            try {
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                $user = JWTAuth::setToken($refreshed)->toUser();
                header('Authorization: Bearer ' . $refreshed);
            } catch (JWTException $e) {
                return response()->json([
                    'code' => 103, // means not refreshable
                    'response' => null // nothing to show
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'code' => 101,//, means auth error in the api,
                'response' => null // nothing to show
            ]);
        }

        // Login the user instance for global usage
        Auth::login($user, false);

        return $next($request);
    }
}
