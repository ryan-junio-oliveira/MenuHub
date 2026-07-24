<?php

namespace Tests\Unit\Services;

use App\Models\DailyMenu;
use App\Models\DailyMenuItem;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Restaurant;
use App\Services\DailyMenuService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyMenuServiceTest extends TestCase
{
    use RefreshDatabase;

    private DailyMenuService $dailyMenuService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dailyMenuService = app(DailyMenuService::class);
    }

    public function test_get_or_create_menu_creates_new_menu(): void
    {
        $restaurant = Restaurant::factory()->create();
        $date = Carbon::today()->format('Y-m-d');

        $menu = $this->dailyMenuService->getOrCreateMenu($restaurant->id, $date);

        $this->assertInstanceOf(DailyMenu::class, $menu);
        $this->assertEquals($restaurant->id, $menu->restaurant_id);
        $this->assertEquals($date, $menu->menu_date->format('Y-m-d'));
    }

    public function test_get_or_create_menu_returns_existing_menu(): void
    {
        $restaurant = Restaurant::factory()->create();
        $date = Carbon::today()->format('Y-m-d');

        $menu1 = $this->dailyMenuService->getOrCreateMenu($restaurant->id, $date);
        $menu2 = $this->dailyMenuService->getOrCreateMenu($restaurant->id, $date);

        $this->assertEquals($menu1->id, $menu2->id);
    }

    public function test_sync_menu_items_replaces_all_items(): void
    {
        $restaurant = Restaurant::factory()->create();
        $category = DishCategory::factory()->for($restaurant)->create();
        $dish1 = Dish::factory()->for($restaurant)->for($category)->create();
        $dish2 = Dish::factory()->for($restaurant)->for($category)->create();

        $menu = $this->dailyMenuService->getOrCreateMenu($restaurant->id, Carbon::today()->format('Y-m-d'));

        $items = [
            ['dish_id' => $dish1->id, 'price_small' => 10.00, 'price_medium' => 15.00, 'price_large' => 20.00],
            ['dish_id' => $dish2->id, 'price_small' => 12.00, 'price_medium' => 18.00],
        ];

        $this->dailyMenuService->syncMenuItems($menu, $items);
        $menu->refresh();

        $this->assertCount(2, $menu->items);
        $this->assertEquals(10.00, $menu->items[0]->price_small);
        $this->assertEquals(15.00, $menu->items[0]->price_medium);
    }

    public function test_sync_menu_items_clears_previous_items(): void
    {
        $restaurant = Restaurant::factory()->create();
        $category = DishCategory::factory()->for($restaurant)->create();
        $dish = Dish::factory()->for($restaurant)->for($category)->create();

        $menu = $this->dailyMenuService->getOrCreateMenu($restaurant->id, Carbon::today()->format('Y-m-d'));

        $this->dailyMenuService->syncMenuItems($menu, [
            ['dish_id' => $dish->id, 'price_medium' => 15.00],
        ]);

        $this->dailyMenuService->syncMenuItems($menu, []);

        $menu->refresh();
        $this->assertCount(0, $menu->items);
    }

    public function test_publish_menu_sets_published_flag(): void
    {
        $restaurant = Restaurant::factory()->create();
        $menu = $this->dailyMenuService->getOrCreateMenu($restaurant->id, Carbon::today()->format('Y-m-d'));

        $this->assertFalse($menu->is_published);

        $this->dailyMenuService->publishMenu($menu);
        $menu->refresh();

        $this->assertTrue($menu->is_published);
    }

    public function test_get_available_dishes_groups_by_category(): void
    {
        $restaurant = Restaurant::factory()->create();
        $category = DishCategory::factory()->for($restaurant)->create(['name' => 'Carnes']);
        Dish::factory()->for($restaurant)->for($category)->count(3)->create(['is_available' => true]);

        $result = $this->dailyMenuService->getAvailableDishes($restaurant->id);

        $this->assertArrayHasKey('Carnes', $result['grouped_dishes']);
        $this->assertCount(3, $result['grouped_dishes']['Carnes']);
    }

    public function test_get_available_dishes_excludes_unavailable(): void
    {
        $restaurant = Restaurant::factory()->create();
        $category = DishCategory::factory()->for($restaurant)->create();
        Dish::factory()->for($restaurant)->for($category)->create(['is_available' => false, 'name' => 'Indisponivel']);
        Dish::factory()->for($restaurant)->for($category)->create(['is_available' => true, 'name' => 'Disponivel']);

        $result = $this->dailyMenuService->getAvailableDishes($restaurant->id);

        $dishes = $result['grouped_dishes']->flatten();
        $this->assertCount(1, $dishes);
        $this->assertEquals('Disponivel', $dishes->first()->name);
    }
}
