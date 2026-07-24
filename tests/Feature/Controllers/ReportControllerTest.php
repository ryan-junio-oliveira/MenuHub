<?php

namespace Tests\Feature\Controllers;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
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

    public function test_index_displays_all_reports(): void
    {
        Order::factory()->for($this->restaurant)->count(2)->create([
            'status' => Order::STATUS_COMPLETED,
            'ordered_at' => Carbon::now()->startOfYear()->addMonth(1),
            'total' => 100,
        ]);

        $response = $this->actingAs($this->user)->get(route('reports.index'));

        $response->assertOk();
        $response->assertViewIs('reports.index');
    }

    public function test_revenue_report_shows_data(): void
    {
        Order::factory()->for($this->restaurant)->create([
            'status' => Order::STATUS_COMPLETED,
            'total' => 100,
            'ordered_at' => Carbon::now()->startOfYear()->addMonth(3),
        ]);

        $response = $this->actingAs($this->user)->get(route('reports.revenue'));

        $response->assertOk();
        $response->assertViewIs('reports.revenue');
    }

    public function test_dishes_report_shows_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.dishes'));

        $response->assertOk();
        $response->assertViewIs('reports.dishes');
    }

    public function test_combinations_report_shows_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.combinations'));

        $response->assertOk();
        $response->assertViewIs('reports.combinations');
    }

    public function test_hours_report_shows_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.hours'));

        $response->assertOk();
        $response->assertViewIs('reports.hours');
    }

    public function test_demand_report_shows_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.demand'));

        $response->assertOk();
        $response->assertViewIs('reports.demand');
    }

    public function test_guest_cannot_access_reports(): void
    {
        $response = $this->get(route('reports.index'));
        $response->assertRedirect(route('login'));
    }
}
