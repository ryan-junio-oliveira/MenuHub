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
            ->orderBy('date', 'desc')
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
            $this->dailyMenuService->syncMenuItems($menu, $validated['dishes']);
        }

        return redirect()->route('daily-menus.index');
    }

    public function show(DailyMenu $dailyMenu)
    {
        $dailyMenu->load('items.dish');

        return view('daily-menus.show', compact('dailyMenu'));
    }

    public function edit(DailyMenu $dailyMenu, Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $dailyMenu->load('items');

        $categories = DishCategory::where('restaurant_id', $restaurantId)
            ->with(['dishes' => fn($q) => $q->where('is_available', true)])
            ->get();

        return view('daily-menus.edit', compact('dailyMenu', 'categories'));
    }

    public function update(StoreDailyMenuRequest $request, DailyMenu $dailyMenu)
    {
        $validated = $request->validated();

        if (isset($validated['dishes'])) {
            $this->dailyMenuService->syncMenuItems($dailyMenu, $validated['dishes']);
        }

        return redirect()->route('daily-menus.index');
    }

    public function publish(DailyMenu $dailyMenu)
    {
        $this->dailyMenuService->publishMenu($dailyMenu);

        return redirect()->route('daily-menus.index');
    }
}
