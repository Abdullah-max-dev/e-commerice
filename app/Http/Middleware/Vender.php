<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Vender
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()?->role !== 'vender') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $next($request);
    }
}
