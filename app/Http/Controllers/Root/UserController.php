<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('restaurant')
            ->where('role', '!=', 'root')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('root.users', compact('users'));
    }

    public function create()
    {
        $restaurants = Restaurant::orderBy('name')->get();

        return view('root.users-form', ['user' => null, 'restaurants' => $restaurants]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:admin,user'],
            'restaurant_id' => ['required', 'exists:restaurants,id'],
        ]);

        $restaurant = Restaurant::with('plan')->find($validated['restaurant_id']);
        $plan = $restaurant->plan;

        if ($plan && $plan->max_users > 0) {
            $currentUsers = User::where('restaurant_id', $restaurant->id)->count();
            if ($currentUsers >= $plan->max_users) {
                return back()->withInput()->with('error', "O plano {$plan->name} permite no máximo {$plan->max_users} usuário(s). Atualmente há {$currentUsers}.");
            }
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'restaurant_id' => $validated['restaurant_id'],
        ]);

        return redirect()->route('root.users')->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        $restaurants = Restaurant::orderBy('name')->get();

        return view('root.users-form', compact('user', 'restaurants'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:admin,user'],
            'restaurant_id' => ['required', 'exists:restaurants,id'],
        ]);

        if ((int) $validated['restaurant_id'] !== $user->restaurant_id) {
            $restaurant = Restaurant::with('plan')->find($validated['restaurant_id']);
            $plan = $restaurant->plan;
            if ($plan && $plan->max_users > 0) {
                $currentUsers = User::where('restaurant_id', $restaurant->id)->count();
                if ($currentUsers >= $plan->max_users) {
                    return back()->withInput()->with('error', "O plano {$plan->name} permite no máximo {$plan->max_users} usuário(s) neste restaurante.");
                }
            }
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'restaurant_id' => $validated['restaurant_id'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('root.users')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'root') {
            return redirect()->route('root.users')->with('error', 'Não é possível excluir um usuário root.');
        }

        $user->delete();

        return redirect()->route('root.users')->with('success', 'Usuário excluído com sucesso!');
    }
}
