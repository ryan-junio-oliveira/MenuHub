<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller extends \Illuminate\Routing\Controller
{
    protected function authorizePlanFeature(Request $request, string $feature): void
    {
        $user = $request->user();
        $restaurant = $user?->restaurant;

        if (!$restaurant || !$restaurant->plan) {
            return;
        }

        if (!$restaurant->plan->hasFeature($feature)) {
            abort(403, "Seu plano não inclui acesso a esta funcionalidade.");
        }
    }
}
