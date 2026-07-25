<?php

namespace Tests\Feature\Controllers;

use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DishControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Restaurant $restaurant;
    private DishCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restaurant = Restaurant::factory()->create();
        $this->user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'role' => 'admin',
        ]);
        $this->category = DishCategory::factory()->for($this->restaurant)->create();
    }

    public function test_index_displays_dishes(): void
    {
        Dish::factory()->for($this->restaurant)->for($this->category)->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('dishes.index'));

        $response->assertOk();
        $response->assertViewIs('dishes.index');
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('dishes.create'));

        $response->assertOk();
        $response->assertViewIs('dishes.create');
    }

    public function test_store_creates_dish(): void
    {
        $response = $this->actingAs($this->user)->post(route('dishes.store'), [
            'name' => 'Frango Grelhado',
            'description' => 'Frango grelhado na chapa',
            'price_small' => 25.90,
            'price_medium' => 29.90,
            'price_large' => 33.90,
            'category_id' => $this->category->id,
            'is_available' => 1,
        ]);

        $response->assertRedirect(route('dishes.index'));
        $this->assertDatabaseHas('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Frango Grelhado',
            'price_small' => 25.90,
            'price_medium' => 29.90,
            'price_large' => 33.90,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('dishes.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'category_id']);
    }

    public function test_edit_displays_form(): void
    {
        $dish = Dish::factory()->for($this->restaurant)->for($this->category)->create();

        $response = $this->actingAs($this->user)->get(route('dishes.edit', $dish));

        $response->assertOk();
        $response->assertViewIs('dishes.edit');
    }

    public function test_update_modifies_dish(): void
    {
        $dish = Dish::factory()->for($this->restaurant)->for($this->category)->create(['name' => 'Antigo']);

        $response = $this->actingAs($this->user)->put(route('dishes.update', $dish), [
            'name' => 'Atualizado',
            'price' => 30.00,
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect(route('dishes.index'));
        $this->assertDatabaseHas('dishes', ['id' => $dish->id, 'name' => 'Atualizado']);
    }

    public function test_destroy_deletes_dish(): void
    {
        $dish = Dish::factory()->for($this->restaurant)->for($this->category)->create();

        $response = $this->actingAs($this->user)->delete(route('dishes.destroy', $dish));

        $response->assertRedirect(route('dishes.index'));
        $this->assertDatabaseMissing('dishes', ['id' => $dish->id]);
    }

    public function test_guest_cannot_access(): void
    {
        $response = $this->get(route('dishes.create'));
        $response->assertRedirect(route('login'));
    }
}
