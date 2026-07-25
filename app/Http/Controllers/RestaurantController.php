<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantSettingsRequest;
use App\Models\Order;
use App\Models\Restaurant;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
}
