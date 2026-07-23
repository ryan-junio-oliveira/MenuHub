<?php

namespace App\Services;

use App\Models\DailyMenu;
use App\Models\DailyMenuItem;
use App\Models\Dish;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyMenuService
{
    public function getOrCreateMenu(int $restaurantId, ?string $date = null): DailyMenu
    {
        $menuDate = $date ? Carbon::parse($date) : Carbon::today();

        return DailyMenu::firstOrCreate(
            ['restaurant_id' => $restaurantId, 'menu_date' => $menuDate->format('Y-m-d')],
            [
                'restaurant_id' => $restaurantId,
                'menu_date' => $menuDate,
                'title' => 'Menu - ' . $menuDate->format('d/m/Y'),
                'is_published' => false,
            ]
        );
    }

    public function syncMenuItems(DailyMenu $menu, array $items): DailyMenu
    {
        DB::transaction(function () use ($menu, $items) {
            $menu->items()->delete();

            foreach ($items as $item) {
                $dish = Dish::findOrFail($item['dish_id']);

                $menu->items()->create([
                    'dish_id' => $dish->id,
                    'size' => $item['size'] ?? 'medium',
                    'price' => $item['price'],
                    'max_selections' => $item['max_selections'] ?? $dish->max_selections,
                    'is_available' => $item['is_available'] ?? true,
                ]);
            }
        });

        return $menu->fresh()->load('items.dish.category');
    }

    public function publishMenu(DailyMenu $menu): DailyMenu
    {
        $menu->update(['is_published' => true]);
        return $menu->fresh();
    }

    public function getAvailableDishes(int $restaurantId, ?int $menuId = null): array
    {
        $dishes = Dish::where('restaurant_id', $restaurantId)
            ->where('is_available', true)
            ->where('is_active', true)
            ->with('category')
            ->orderBy('dish_category_id')
            ->get()
            ->groupBy(fn($dish) => $dish->category->name);

        $selectedDishIds = collect();
        if ($menuId) {
            $selectedDishIds = DailyMenuItem::where('daily_menu_id', $menuId)
                ->pluck('dish_id');
        }

        return [
            'grouped_dishes' => $dishes,
            'selected_dish_ids' => $selectedDishIds,
        ];
    }
}
