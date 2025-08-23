<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class Checkisadmin
{
   /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || $user->is_admin == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مخصص لك',
            ], 403);
        }

        return $next($request);
    }
}

