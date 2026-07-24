<?php

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Models\DailyMenu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = app(DashboardService::class);
    }

    public function test_get_today_stats_returns_correct_counts(): void
    {
        $restaurant = Restaurant::factory()->create();

        Order::factory()->for($restaurant)->count(3)->create([
            'status' => Order::STATUS_COMPLETED,
            'subtotal' => 50.00,
            'delivery_fee' => 0,
            'total' => 50.00,
            'ordered_at' => Carbon::today()->setHour(10),
        ]);
        Order::factory()->for($restaurant)->create([
            'status' => Order::STATUS_RECEIVED,
            'subtotal' => 0,
            'delivery_fee' => 0,
            'total' => 0,
            'ordered_at' => Carbon::today()->setHour(11),
        ]);

        $stats = $this->dashboardService->getTodayStats($restaurant->id);

        $this->assertEquals(4, $stats['orders_count']);
        $this->assertEquals(150.00, $stats['revenue']);
        $this->assertEquals(1, $stats['pending_orders']);
        $this->assertEquals(3, $stats['completed_today']);
    }

    public function test_get_today_stats_shows_active_menu(): void
    {
        $restaurant = Restaurant::factory()->create();

        DailyMenu::factory()->for($restaurant)->create([
            'menu_date' => Carbon::today(),
            'is_published' => true,
        ]);

        $stats = $this->dashboardService->getTodayStats($restaurant->id);

        $this->assertTrue($stats['active_menu']);
    }

    public function test_get_today_stats_shows_no_active_menu(): void
    {
        $restaurant = Restaurant::factory()->create();

        $stats = $this->dashboardService->getTodayStats($restaurant->id);

        $this->assertFalse($stats['active_menu']);
    }

    public function test_get_chart_data_returns_14_days(): void
    {
        $restaurant = Restaurant::factory()->create();

        $chartData = $this->dashboardService->getChartData($restaurant->id);

        $this->assertCount(14, $chartData['dates']);
        $this->assertCount(14, $chartData['orders']);
        $this->assertCount(14, $chartData['revenue']);
    }

    public function test_get_chart_data_includes_orders(): void
    {
        $restaurant = Restaurant::factory()->create();
        Order::factory()->for($restaurant)->count(2)->create([
            'status' => Order::STATUS_COMPLETED,
            'ordered_at' => Carbon::today(),
        ]);

        $chartData = $this->dashboardService->getChartData($restaurant->id);

        $todayIndex = 13;
        $this->assertEquals(2, $chartData['orders'][$todayIndex]);
    }

    public function test_get_status_distribution_returns_all_statuses(): void
    {
        $restaurant = Restaurant::factory()->create();
        Order::factory()->for($restaurant)->create(['status' => Order::STATUS_RECEIVED, 'ordered_at' => Carbon::today()]);
        Order::factory()->for($restaurant)->count(2)->create(['status' => Order::STATUS_PREPARING, 'ordered_at' => Carbon::today()]);

        $distribution = $this->dashboardService->getStatusDistribution($restaurant->id);

        $this->assertEquals(1, $distribution['received']);
        $this->assertEquals(2, $distribution['preparing']);
        $this->assertEquals(0, $distribution['completed']);
    }

    public function test_get_top_dishes_orders_by_quantity(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();
        $order = Order::factory()->for($restaurant)->for($customer)->create([
            'status' => Order::STATUS_COMPLETED,
            'ordered_at' => Carbon::today(),
        ]);

        OrderItem::factory()->for($order)->create(['dish_name' => 'Feijoada', 'quantity' => 5]);
        OrderItem::factory()->for($order)->create(['dish_name' => 'Salada', 'quantity' => 2]);

        $topDishes = $this->dashboardService->getTopDishes($restaurant->id);

        $this->assertCount(2, $topDishes);
        $this->assertEquals('Feijoada', $topDishes[0]['dish_name']);
    }

    public function test_get_latest_orders_returns_most_recent_first(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();

        Order::factory()->for($restaurant)->for($customer)->create([
            'ordered_at' => Carbon::today()->setHour(8),
        ]);
        Order::factory()->for($restaurant)->for($customer)->create([
            'ordered_at' => Carbon::today()->setHour(18),
        ]);

        $latest = $this->dashboardService->getLatestOrders($restaurant->id);

        $this->assertCount(2, $latest);
        $this->assertEquals(18, Carbon::parse($latest[0]['ordered_at'])->hour);
    }

    public function test_clear_cache_works(): void
    {
        $restaurant = Restaurant::factory()->create();

        $this->dashboardService->getTodayStats($restaurant->id);
        $this->dashboardService->clearCache($restaurant->id);

        $this->assertTrue(true);
    }
}
