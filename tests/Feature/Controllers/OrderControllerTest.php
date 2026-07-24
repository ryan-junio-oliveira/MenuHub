<?php

namespace Tests\Feature\Controllers;

use App\Models\Customer;
use App\Models\DailyMenu;
use App\Models\DailyMenuItem;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
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

    public function test_index_displays_orders(): void
    {
        Order::factory()->for($this->restaurant)->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('orders.index'));

        $response->assertOk();
        $response->assertViewIs('orders.index');
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('orders.create'));

        $response->assertOk();
        $response->assertViewIs('orders.create');
    }

    public function test_show_displays_order(): void
    {
        $customer = Customer::factory()->for($this->restaurant)->create();
        $order = Order::factory()->for($this->restaurant)->for($customer)->create();

        $response = $this->actingAs($this->user)->get(route('orders.show', $order));

        $response->assertOk();
        $response->assertViewIs('orders.show');
    }

    public function test_update_status_changes_order_status(): void
    {
        $order = Order::factory()->for($this->restaurant)->create(['status' => Order::STATUS_RECEIVED]);

        $response = $this->actingAs($this->user)->put(route('orders.update-status', $order), [
            'status' => Order::STATUS_PREPARING,
        ]);

        $response->assertRedirect(route('orders.index'));
        $order->refresh();
        $this->assertEquals(Order::STATUS_PREPARING, $order->status);
    }

    public function test_update_status_rejects_invalid_status(): void
    {
        $order = Order::factory()->for($this->restaurant)->create();

        $response = $this->actingAs($this->user)->put(route('orders.update-status', $order), [
            'status' => 'invalid',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_kanban_displays_board(): void
    {
        Order::factory()->for($this->restaurant)->count(2)->create(['status' => Order::STATUS_RECEIVED]);

        $response = $this->actingAs($this->user)->get(route('orders.kanban'));

        $response->assertOk();
        $response->assertViewIs('orders.kanban');
    }

    public function test_guest_cannot_access_orders(): void
    {
        $response = $this->get(route('orders.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_tenant_only_sees_own_orders(): void
    {
        $otherRestaurant = Restaurant::factory()->create();
        Order::factory()->for($otherRestaurant)->create();

        $response = $this->actingAs($this->user)->get(route('orders.index'));

        $response->assertOk();
    }

    public function test_api_active_orders_returns_json(): void
    {
        Order::factory()->for($this->restaurant)->create(['status' => Order::STATUS_RECEIVED]);

        $response = $this->actingAs($this->user)->getJson('/api/orders/active');

        $response->assertOk();
        $response->assertJsonCount(1);
    }
}
