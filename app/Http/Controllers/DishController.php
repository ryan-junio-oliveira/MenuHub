<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DishController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $dishes = Dish::where('restaurant_id', $restaurantId)
            ->with('category')
            ->orderBy('dish_category_id')
            ->get();

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
            'price_small' => ['nullable', 'numeric', 'min:0'],
            'price_medium' => ['nullable', 'numeric', 'min:0'],
            'price_large' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:dish_categories,id'],
            'is_available' => ['boolean'],
            'is_gourmet' => ['boolean'],
            'max_selections' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data = [
            'restaurant_id' => $restaurantId,
            'dish_category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price_small' => $validated['price_small'] ?? null,
            'price_medium' => $validated['price_medium'] ?? $validated['price_small'] ?? null,
            'price_large' => $validated['price_large'] ?? $validated['price_medium'] ?? $validated['price_small'] ?? null,
            'is_available' => $validated['is_available'] ?? true,
            'is_gourmet' => $validated['is_gourmet'] ?? false,
            'max_selections' => $validated['max_selections'] ?? 1,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store("dishes/{$restaurantId}", 'public');
        }

        Dish::create($data);

        return redirect()->route('dishes.index')->with('success', 'Prato criado com sucesso!');
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
            'price_small' => ['nullable', 'numeric', 'min:0'],
            'price_medium' => ['nullable', 'numeric', 'min:0'],
            'price_large' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:dish_categories,id'],
            'is_available' => ['boolean'],
            'is_gourmet' => ['boolean'],
            'max_selections' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data = [
            'dish_category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price_small' => $validated['price_small'] ?? null,
            'price_medium' => $validated['price_medium'] ?? $validated['price_small'] ?? null,
            'price_large' => $validated['price_large'] ?? $validated['price_medium'] ?? $validated['price_small'] ?? null,
            'is_available' => $validated['is_available'] ?? true,
            'is_gourmet' => $validated['is_gourmet'] ?? false,
            'max_selections' => $validated['max_selections'] ?? 1,
        ];

        if ($request->hasFile('image')) {
            if ($dish->image) {
                Storage::disk('public')->delete($dish->image);
            }
            $data['image'] = $request->file('image')->store("dishes/{$dish->restaurant_id}", 'public');
        }

        $dish->update($data);

        return redirect()->route('dishes.index')->with('success', 'Prato atualizado com sucesso!');
    }

    public function destroy(Dish $dish)
    {
        if ($dish->image) {
            Storage::disk('public')->delete($dish->image);
        }

        $dish->delete();

        return redirect()->route('dishes.index')->with('success', 'Prato excluído com sucesso!');
    }
}
