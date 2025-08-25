<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class CheckActivationCode
 *
 * Middleware to ensure that the user has activated their account.
 * If the user is not logged in or their account is inactive, 
 * access to the requested resource will be denied.
 */
class CheckActivationCode
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

        // If user is not authenticated or not active, block access
        if (!$user || $user->is_active == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'يجب تفعيل الحساب للوصول إلى هذا المورد.', // Account must be activated
            ], 403);
        }

        // User is active, proceed with the request
        return $next($request);
    }
}
