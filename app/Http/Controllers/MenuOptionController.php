<?php

namespace App\Http\Controllers;

use App\Models\DishCategory;
use App\Models\MenuOption;
use Illuminate\Http\Request;

class MenuOptionController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $options = MenuOption::where('restaurant_id', $restaurantId)
            ->with('category')
            ->orderBy('display_order')
            ->get();

        return view('menu-options.index', compact('options'));
    }

    public function create(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;
        $categories = DishCategory::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('menu-options.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:dish_categories,id'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);

        MenuOption::create([
            'restaurant_id' => $restaurantId,
            'dish_category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        return redirect()->route('menu-options.index')->with('success', 'Opcao criada com sucesso!');
    }

    public function show(MenuOption $menuOption)
    {
        $menuOption->load('category');
        return view('menu-options.show', ['option' => $menuOption]);
    }

    public function edit(Request $request, MenuOption $menuOption)
    {
        $restaurantId = $request->user()->restaurant_id;
        $categories = DishCategory::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('menu-options.edit', ['option' => $menuOption, 'categories' => $categories]);
    }

    public function update(Request $request, MenuOption $menuOption)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:dish_categories,id'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $menuOption->update([
            'name' => $validated['name'],
            'dish_category_id' => $validated['category_id'],
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? $menuOption->is_active,
        ]);

        return redirect()->route('menu-options.index')->with('success', 'Opcao atualizada com sucesso!');
    }

    public function destroy(MenuOption $menuOption)
    {
        $menuOption->delete();
        return redirect()->route('menu-options.index')->with('success', 'Opcao excluida com sucesso!');
    }
}
