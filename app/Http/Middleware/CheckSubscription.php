<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role === 'root') {
            return $next($request);
        }

        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return $next($request);
        }

        if ($restaurant->subscription_status === 'trial' && $restaurant->trial_ends_at) {
            if (Carbon::parse($restaurant->trial_ends_at)->isPast()) {
                $restaurant->update(['subscription_status' => 'expired']);
            }
        }

        if (in_array($restaurant->subscription_status, ['expired', 'canceled'])) {
            return redirect()->route('subscription.expired')
                ->with('error', 'Sua assinatura está ' . ($restaurant->subscription_status === 'expired' ? 'expirada' : 'cancelada') . '. Renove para continuar usando o sistema.');
        }

        return $next($request);
    }
}
