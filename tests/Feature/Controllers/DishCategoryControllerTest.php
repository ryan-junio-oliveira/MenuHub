<?php

namespace Tests\Feature\Controllers;

use App\Models\DishCategory;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DishCategoryControllerTest extends TestCase
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

    public function test_index_displays_categories(): void
    {
        DishCategory::factory()->for($this->restaurant)->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('dish-categories.index'));

        $response->assertOk();
        $response->assertViewIs('dish-categories.index');
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('dish-categories.create'));

        $response->assertOk();
        $response->assertViewIs('dish-categories.create');
    }

    public function test_store_creates_category(): void
    {
        $response = $this->actingAs($this->user)->post(route('dish-categories.store'), [
            'name' => 'Carnes',
            'description' => 'Pratos com carne bovina',
        ]);

        $response->assertRedirect(route('dish-categories.index'));
        $this->assertDatabaseHas('dish_categories', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Carnes',
        ]);
    }

    public function test_store_validates_required_name(): void
    {
        $response = $this->actingAs($this->user)->post(route('dish-categories.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_edit_displays_form(): void
    {
        $category = DishCategory::factory()->for($this->restaurant)->create();

        $response = $this->actingAs($this->user)->get(route('dish-categories.edit', $category));

        $response->assertOk();
        $response->assertViewIs('dish-categories.edit');
    }

    public function test_update_modifies_category(): void
    {
        $category = DishCategory::factory()->for($this->restaurant)->create(['name' => 'Antigo']);

        $response = $this->actingAs($this->user)->put(route('dish-categories.update', $category), [
            'name' => 'Atualizado',
        ]);

        $response->assertRedirect(route('dish-categories.index'));
        $this->assertDatabaseHas('dish_categories', ['id' => $category->id, 'name' => 'Atualizado']);
    }

    public function test_destroy_deletes_category(): void
    {
        $category = DishCategory::factory()->for($this->restaurant)->create();

        $response = $this->actingAs($this->user)->delete(route('dish-categories.destroy', $category));

        $response->assertRedirect(route('dish-categories.index'));
        $this->assertDatabaseMissing('dish_categories', ['id' => $category->id]);
    }

    public function test_guest_redirected_to_login(): void
    {
        $response = $this->get(route('dish-categories.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_tenant_scopes_results_to_own_restaurant(): void
    {
        $otherRestaurant = Restaurant::factory()->create();
        DishCategory::factory()->for($otherRestaurant)->create(['name' => 'Outro Restaurante']);

        $response = $this->actingAs($this->user)->get(route('dish-categories.index'));

        $response->assertOk();
        $response->assertDontSee('Outro Restaurante');
    }
}
