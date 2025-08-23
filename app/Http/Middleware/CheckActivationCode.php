<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActivationCode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || $user->is_active == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'يجب تفعيل الحساب للوصول إلى هذا المورد.',
            ], 403);
        }

        return $next($request);
    }
}
