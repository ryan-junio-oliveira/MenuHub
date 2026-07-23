<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'root') {
            return $next($request);
        }

        if ($user && !$user->restaurant_id) {
            return redirect()->route('restaurant.create')
                ->with('warning', 'Please create a restaurant to get started.');
        }

        return $next($request);
    }
}
