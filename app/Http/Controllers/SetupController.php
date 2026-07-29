<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SetupController extends Controller
{
    public function show(string $token)
    {
        $restaurant = Restaurant::where('setup_token', $token)->firstOrFail();

        if ($restaurant->isSetupComplete()) {
            return redirect()->route('login')
                ->with('info', 'Este cadastro já foi finalizado. Faça login para continuar.');
        }

        $admin = $restaurant->adminUser();

        if (!$admin) {
            abort(404, 'Administrador não encontrado.');
        }

        return view('setup.complete', compact('restaurant', 'admin', 'token'));
    }

    public function complete(Request $request, string $token)
    {
        $restaurant = Restaurant::where('setup_token', $token)->firstOrFail();

        if ($restaurant->isSetupComplete()) {
            return redirect()->route('login')
                ->with('info', 'Este cadastro já foi finalizado. Faça login para continuar.');
        }

        $admin = $restaurant->adminUser();

        if (!$admin) {
            abort(404, 'Administrador não encontrado.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($restaurant, $admin, $validated) {
            $restaurant->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'setup_token' => null,
                'setup_completed_at' => now(),
                'is_active' => true,
            ]);

            $admin->update([
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);
        });

        Auth::login($admin);

        return redirect()->route('dashboard')
            ->with('success', 'Cadastro completo! Bem-vindo ao MenuHub.');
    }
}