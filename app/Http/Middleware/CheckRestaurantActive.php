<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRestaurantActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role === 'root') {
            return $next($request);
        }

        $restaurant = $user->restaurant;

        if ($restaurant && !$restaurant->is_active) {
            abort(403, 'Este restaurante está desativado. Contate o administrador.');
        }

        return $next($request);
    }
}
