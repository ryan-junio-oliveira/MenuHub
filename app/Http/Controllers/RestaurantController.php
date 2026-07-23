<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantSettingsRequest;
use App\Models\Restaurant;
use App\Services\SettingService;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {}

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

    public function edit(Request $request)
    {
        $restaurant = $request->user()->restaurant;

        return view('restaurant.edit', compact('restaurant'));
    }

    public function update(UpdateRestaurantSettingsRequest $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $validated = $request->validated();
        $restaurant = $request->user()->restaurant;
        $restaurant->update($validated);

        return redirect()->route('dashboard');
    }
}
