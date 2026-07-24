<?php

namespace Tests\Feature\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
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

    public function test_dashboard_displays_stats(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard.index');
    }

    public function test_guest_redirected(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_root_dashboard_shows_system_stats(): void
    {
        $rootUser = User::factory()->create(['role' => 'root', 'restaurant_id' => null]);

        $response = $this->actingAs($rootUser)->get(route('root.dashboard'));

        $response->assertOk();
        $response->assertViewIs('root.dashboard');
    }

    public function test_regular_user_cannot_access_root(): void
    {
        $response = $this->actingAs($this->user)->get(route('root.dashboard'));

        $response->assertForbidden();
    }
}
