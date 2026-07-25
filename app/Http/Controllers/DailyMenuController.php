<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyMenuRequest;
use App\Models\DailyMenu;
use App\Models\DishCategory;
use App\Services\DailyMenuService;
use Illuminate\Http\Request;

class DailyMenuController extends Controller
{
    public function __construct(
        private readonly DailyMenuService $dailyMenuService,
    ) {}

    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $menus = DailyMenu::where('restaurant_id', $restaurantId)
            ->orderBy('menu_date', 'desc')
            ->get();

        return view('daily-menus.index', compact('menus'));
    }

    public function create(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $categories = DishCategory::where('restaurant_id', $restaurantId)
            ->with(['dishes' => fn($q) => $q->where('is_available', true)])
            ->get();

        return view('daily-menus.create', compact('categories'));
    }

    public function store(StoreDailyMenuRequest $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $validated = $request->validated();

        $menu = $this->dailyMenuService->getOrCreateMenu(
            $restaurantId,
            $validated['date'],
        );

        if (isset($validated['dishes'])) {
            $items = collect($validated['dishes'])->map(fn($dishId) => [
                'dish_id' => $dishId,
                'price_small' => $validated['price_small'][$dishId] ?? null,
                'price_medium' => $validated['price_medium'][$dishId] ?? null,
                'price_large' => $validated['price_large'][$dishId] ?? null,
            ])->all();

            $this->dailyMenuService->syncMenuItems($menu, $items);
        }

        return redirect()->route('daily-menus.index')->with('success', 'Cardápio criado com sucesso!');
    }

    public function update(StoreDailyMenuRequest $request, DailyMenu $dailyMenu)
    {
        $validated = $request->validated();

        if (isset($validated['dishes'])) {
            $items = collect($validated['dishes'])->map(fn($dishId) => [
                'dish_id' => $dishId,
                'price_small' => $validated['price_small'][$dishId] ?? null,
                'price_medium' => $validated['price_medium'][$dishId] ?? null,
                'price_large' => $validated['price_large'][$dishId] ?? null,
            ])->all();

            $this->dailyMenuService->syncMenuItems($dailyMenu, $items);
        } else {
            $dailyMenu->items()->delete();
        }

        return redirect()->route('daily-menus.index')->with('success', 'Cardápio atualizado com sucesso!');
    }

    public function show(DailyMenu $dailyMenu)
    {
        $dailyMenu->load('items.dish');

        return view('daily-menus.show', ['menu' => $dailyMenu]);
    }

    public function edit(DailyMenu $dailyMenu, Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $dailyMenu->load('items');

        $categories = DishCategory::where('restaurant_id', $restaurantId)
            ->with(['dishes' => fn($q) => $q->where('is_available', true)])
            ->get();

        return view('daily-menus.edit', ['menu' => $dailyMenu, 'categories' => $categories]);
    }

    public function publish(DailyMenu $dailyMenu)
    {
        $this->dailyMenuService->publishMenu($dailyMenu);

        return redirect()->route('daily-menus.index')->with('success', 'Cardápio publicado com sucesso!');
    }

    public function destroy(DailyMenu $dailyMenu)
    {
        $dailyMenu->items()->delete();
        $dailyMenu->delete();

        return redirect()->route('daily-menus.index')->with('success', 'Cardápio excluído com sucesso!');
    }
}
