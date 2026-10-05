<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // ✅ សម្រាប់ API Requests → Return null (Return 401 JSON)
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // ✅ សម្រាប់ Web Requests → Redirect ទៅ Login
        return route('login');
    }
}
