<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!auth()->check() || !auth()->user()->hasRole(...$roles)) {
            abort(403, 'Unauthorized — insufficient role.');
        }

        return $next($request);
    }
}
