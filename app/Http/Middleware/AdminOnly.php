<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Admins only');
        return $next($request);
    }
}
