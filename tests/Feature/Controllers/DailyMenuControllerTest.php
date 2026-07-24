<?php

namespace Tests\Feature\Controllers;

use App\Models\DailyMenu;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyMenuControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restaurant = Restaurant::factory()->create();
        $this->user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'role' => 'admin',
        ]);
    }

    public function test_index_displays_menus(): void
    {
        DailyMenu::factory()->for($this->restaurant)->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('daily-menus.index'));

        $response->assertOk();
        $response->assertViewIs('daily-menus.index');
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('daily-menus.create'));

        $response->assertOk();
        $response->assertViewIs('daily-menus.create');
    }

    public function test_store_creates_menu_with_dishes(): void
    {
        $category = DishCategory::factory()->for($this->restaurant)->create();
        $dish = Dish::factory()->for($this->restaurant)->for($category)->create();

        $response = $this->actingAs($this->user)->post(route('daily-menus.store'), [
            'date' => Carbon::today()->format('Y-m-d'),
            'dishes' => [$dish->id],
            'price_medium' => [$dish->id => 20.00],
        ]);

        $response->assertRedirect(route('daily-menus.index'));
        $this->assertDatabaseHas('daily_menus', [
            'restaurant_id' => $this->restaurant->id,
            'menu_date' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    public function test_store_validates_date(): void
    {
        $response = $this->actingAs($this->user)->post(route('daily-menus.store'), [
            'date' => '',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_show_displays_menu(): void
    {
        $menu = DailyMenu::factory()->for($this->restaurant)->create();

        $response = $this->actingAs($this->user)->get(route('daily-menus.show', $menu));

        $response->assertOk();
        $response->assertViewIs('daily-menus.show');
    }

    public function test_edit_displays_form(): void
    {
        $menu = DailyMenu::factory()->for($this->restaurant)->create();

        $response = $this->actingAs($this->user)->get(route('daily-menus.edit', $menu));

        $response->assertOk();
        $response->assertViewIs('daily-menus.edit');
    }

    public function test_publish_updates_status(): void
    {
        $menu = DailyMenu::factory()->for($this->restaurant)->create(['is_published' => false]);

        $response = $this->actingAs($this->user)->put(route('daily-menus.publish', $menu));

        $response->assertRedirect(route('daily-menus.index'));
        $menu->refresh();
        $this->assertTrue($menu->is_published);
    }

    public function test_guest_cannot_access(): void
    {
        $response = $this->get(route('daily-menus.index'));
        $response->assertRedirect(route('login'));
    }
}
