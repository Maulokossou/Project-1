<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('sanctum')->check() || !Auth::guard('sanctum')->user() instanceof \App\Models\Company) {
            return response()->json(['message' => 'Unauthorized. Company access required.'], 403);
        }

        return $next($request);
    }
}