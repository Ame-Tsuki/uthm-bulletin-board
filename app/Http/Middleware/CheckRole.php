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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        
        if (!$user) {
            abort(403, 'Unauthorized. Please login.');
        }
        
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized access. Required role: ' . implode(', ', $roles));
        }
        
        return $next($request);
    }
}