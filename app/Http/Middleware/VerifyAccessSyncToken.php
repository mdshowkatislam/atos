<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccessSyncToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle($request, Closure $next)
{
    $token = $request->bearerToken();

    if ($token !== config('services.access_sync.token')) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    return $next($request);
}
}
