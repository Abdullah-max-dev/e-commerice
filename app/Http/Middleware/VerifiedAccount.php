<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifiedAccount
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->verification_status !== 'verified') {
            return response()->json([
                'message' => 'Your account must be verified to access this feature.',
            ], 403);
        }

        return $next($request);
    }
}
