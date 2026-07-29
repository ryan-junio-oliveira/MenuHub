<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantSettingsRequest;
use App\Mail\RestaurantInvitation;
use App\Models\Customer;
use App\Models\DailyMenu;
use App\Models\Delivery;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Setting;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    public function index()
    {
        $restaurants = Restaurant::withCount(['users', 'orders'])->orderBy('name')->get();

        return view('root.restaurants', compact('restaurants'));
    }

    public function rootCreate()
    {
        return view('root.restaurants-create');
    }

    public function rootStore(Request $request)
    {
        $validated = $request->validate([
            'razao_social' => ['required', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        ]);

        $setupToken = Str::random(64);

        $restaurant = DB::transaction(function () use ($validated, $setupToken) {
            $restaurant = Restaurant::create([
                'name' => $validated['razao_social'],
                'razao_social' => $validated['razao_social'],
                'email' => $validated['admin_email'],
                'is_active' => false,
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(30),
                'setup_token' => $setupToken,
            ]);

            User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make(Str::random(32)),
                'role' => 'admin',
                'restaurant_id' => $restaurant->id,
            ]);

            return $restaurant;
        });

        $admin = $restaurant->adminUser();
        $setupUrl = route('setup.show', $setupToken);

        Mail::to($admin->email)->send(new RestaurantInvitation($restaurant, $admin, $setupUrl));

        return redirect()->route('root.restaurants.index')
            ->with('success', "Restaurante '{$restaurant->razao_social}' criado! Um convite foi enviado para <strong>{$admin->email}</strong>.");
    }

    public function create()
    {
        return view('restaurant.create');
    }

    public function store(StoreRestaurantRequest $request)
    {
        $validated = $request->validated();

        $restaurant = Restaurant::create($validated);

        $request->user()->restaurant_id = $restaurant->id;
        $request->user()->save();

        return redirect()->route('dashboard');
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->loadCount(['users', 'orders', 'customers', 'dishes']);

        $recentOrders = Order::where('restaurant_id', $restaurant->id)
            ->with('customer')
            ->latest()
            ->limit(10)
            ->get();

        $users = $restaurant->users()->orderBy('name')->get();

        $revenue = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'completed')
            ->sum('total');

        $monthOrders = Order::where('restaurant_id', $restaurant->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('root.restaurant-show', compact(
            'restaurant', 'recentOrders', 'users', 'revenue', 'monthOrders'
        ));
    }

    public function edit(Request $request)
    {
        $restaurant = $request->user()->restaurant;

        return view('restaurant.edit', compact('restaurant'));
    }

    public function update(UpdateRestaurantSettingsRequest $request)
    {
        $restaurant = $request->user()->restaurant;

        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $validated['logo'] = $request->file('logo')->store("restaurants/{$restaurant->id}", 'public');
        }

        if ($request->hasFile('cover')) {
            if ($restaurant->cover) {
                Storage::disk('public')->delete($restaurant->cover);
            }
            $validated['cover'] = $request->file('cover')->store("restaurants/{$restaurant->id}", 'public');
        }

        if ($request->filled('whatsapp_phone_id')) {
            $validated['whatsapp_phone_id'] = $request->whatsapp_phone_id;
        }

        if ($request->filled('whatsapp_api_token') && $request->whatsapp_api_token !== '********') {
            $validated['whatsapp_api_token'] = $request->whatsapp_api_token;
        }

        $restaurant->update($validated);

        return redirect()->route('restaurant.edit')->with('success', 'Restaurante atualizado com sucesso!');
    }

    public function toggleActive(Restaurant $restaurant)
    {
        $restaurant->update(['is_active' => !$restaurant->is_active]);

        return redirect()->back()->with('success',
            $restaurant->is_active
                ? 'Restaurante ativado com sucesso!'
                : 'Restaurante desativado com sucesso!'
        );
    }

    public function destroy(Restaurant $restaurant)
    {
        DB::transaction(function () use ($restaurant) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            if ($restaurant->cover) {
                Storage::disk('public')->delete($restaurant->cover);
            }

            $restaurant->users()->each(fn(User $user) => $user->delete());

            $restaurant->customers()->each(function (Customer $customer) {
                $customer->tags()->detach();
                $customer->orders()->each(fn(Order $order) => $order->items()->delete());
                $customer->orders()->delete();
                $customer->delete();
            });

            $restaurant->orders()->each(fn(Order $order) => $order->items()->delete());
            $restaurant->orders()->delete();

            $restaurant->dailyMenus()->each(fn(DailyMenu $menu) => $menu->items()->delete());
            $restaurant->dailyMenus()->delete();

            $restaurant->dishes()->delete();
            $restaurant->dishCategories()->delete();
            $restaurant->deliveries()->delete();
            $restaurant->payments()->delete();
            $restaurant->settings()->delete();
            $restaurant->invoices()->delete();

            $restaurant->delete();
        });

        return redirect()->route('root.restaurants.index')
            ->with('success', 'Restaurante excluído com sucesso!');
    }
}