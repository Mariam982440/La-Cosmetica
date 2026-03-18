<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next,$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role->value, $roles)) {
            return response()->json(['message' => 'Accès refusé. Permissions insuffisantes.'], 403);
        }
        return $next($request);    
    }
}
