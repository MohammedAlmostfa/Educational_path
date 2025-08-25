<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class CheckIsAdmin
 *
 * Middleware to ensure that the authenticated user has admin privileges.
 * If the user is not logged in or not an admin, access to the resource is denied.
 */
class CheckIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the currently authenticated user via Sanctum
        $user = Auth::guard('sanctum')->user();

        // If user is not authenticated or not an admin, block access
        if (!$user || $user->is_admin == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مخصص لك', // Not authorized for this
            ], 403);
        }

        // User is admin, proceed with the request
        return $next($request);
    }
}
