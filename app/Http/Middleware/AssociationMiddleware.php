<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssociationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('sanctum')->check() || !Auth::guard('sanctum')->user() instanceof \App\Models\Association) {
            return response()->json(['message' => 'Unauthorized. Association access required.'], 403);
        }

        return $next($request);
    }
}