<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishCategory;
use Illuminate\Http\Request;

class DishController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $dishes = Dish::where('restaurant_id', $restaurantId)->get();

        return view('dishes.index', compact('dishes'));
    }

    public function create(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $categories = DishCategory::where('restaurant_id', $restaurantId)->get();

        return view('dishes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:dish_categories,id'],
            'is_available' => ['boolean'],
        ]);

        Dish::create([
            'restaurant_id' => $restaurantId,
            'dish_category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price_small' => $validated['price'],
            'price_medium' => $validated['price'],
            'price_large' => $validated['price'],
            'is_available' => $validated['is_available'] ?? true,
        ]);

        return redirect()->route('dishes.index');
    }

    public function edit(Dish $dish, Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $categories = DishCategory::where('restaurant_id', $restaurantId)->get();

        return view('dishes.edit', compact('dish', 'categories'));
    }

    public function update(Request $request, Dish $dish)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:dish_categories,id'],
            'is_available' => ['boolean'],
        ]);

        $dish->update([
            'dish_category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price_small' => $validated['price'],
            'price_medium' => $validated['price'],
            'price_large' => $validated['price'],
            'is_available' => $validated['is_available'] ?? true,
        ]);

        return redirect()->route('dishes.index');
    }

    public function destroy(Dish $dish)
    {
        $dish->delete();

        return redirect()->route('dishes.index');
    }
}
