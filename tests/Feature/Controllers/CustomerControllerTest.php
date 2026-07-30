<?php

namespace Tests\Feature\Controllers;

use App\Models\Customer;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
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

    public function test_index_displays_customers(): void
    {
        Customer::factory()->for($this->restaurant)->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('customers.index'));

        $response->assertOk();
        $response->assertViewIs('customers.index');
    }

    public function test_show_displays_customer(): void
    {
        $customer = Customer::factory()->for($this->restaurant)->create();

        $response = $this->actingAs($this->user)->get(route('customers.show', $customer));

        $response->assertOk();
        $response->assertViewIs('customers.show');
    }

    public function test_edit_displays_form(): void
    {
        $customer = Customer::factory()->for($this->restaurant)->create();

        $response = $this->actingAs($this->user)->get(route('customers.edit', $customer));

        $response->assertOk();
        $response->assertViewIs('customers.edit');
    }

    public function test_update_modifies_customer(): void
    {
        $customer = Customer::factory()->for($this->restaurant)->create(['name' => 'Antigo']);

        $response = $this->actingAs($this->user)->put(route('customers.update', $customer), [
            'name' => 'Atualizado',
        ]);

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Atualizado']);
    }

    public function test_destroy_deletes_customer(): void
    {
        $customer = Customer::factory()->for($this->restaurant)->create();

        $response = $this->actingAs($this->user)->delete(route('customers.destroy', $customer));

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_search_returns_json(): void
    {
        Customer::factory()->for($this->restaurant)->create(['name' => 'Maria Souza']);

        $response = $this->actingAs($this->user)->get(route('customers.search', ['q' => 'Maria']));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Maria Souza']);
    }

    public function test_tenant_only_sees_own_customers(): void
    {
        $otherRestaurant = Restaurant::factory()->create();
        Customer::factory()->for($otherRestaurant)->create(['name' => 'Outro Cliente']);

        $response = $this->actingAs($this->user)->get(route('customers.index'));

        $response->assertOk();
        $response->assertDontSee('Outro Cliente');
    }
}
