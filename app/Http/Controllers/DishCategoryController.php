<?php

namespace App\Http\Controllers;

use App\Models\DishCategory;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DishCategoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $categories = DishCategory::where('restaurant_id', $restaurantId)->get();

        return view('dish-categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', DishCategory::class);

        return view('dish-categories.create');
    }

    public function show(DishCategory $dishCategory)
    {
        $this->authorize('view', $dishCategory);

        $dishes = $dishCategory->dishes()->orderBy('name')->get();

        return view('dish-categories.show', compact('dishCategory', 'dishes'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', DishCategory::class);

        $restaurantId = $request->user()->restaurant_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category = DishCategory::create([
            ...$validated,
            'restaurant_id' => $restaurantId,
        ]);

        return redirect()->route('dish-categories.index');
    }

    public function edit(DishCategory $dishCategory)
    {
        $this->authorize('update', $dishCategory);

        return view('dish-categories.edit', ['category' => $dishCategory]);
    }

    public function update(Request $request, DishCategory $dishCategory)
    {
        $this->authorize('update', $dishCategory);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $dishCategory->update($validated);

        return redirect()->route('dish-categories.index');
    }

    public function destroy(DishCategory $dishCategory)
    {
        $this->authorize('delete', $dishCategory);

        $dishCategory->delete();

        return redirect()->route('dish-categories.index');
    }
}
