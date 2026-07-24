<?php

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportService $reportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportService = app(ReportService::class);
    }

    public function test_get_revenue_report_returns_monthly_data(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();

        Order::factory()->for($restaurant)->for($customer)->create([
            'total' => 100.00,
            'status' => Order::STATUS_COMPLETED,
            'ordered_at' => Carbon::now()->startOfYear()->addMonth(2),
        ]);

        $report = $this->reportService->getRevenueReport($restaurant->id, 'monthly', Carbon::now()->year);

        $this->assertCount(12, $report);
        $this->assertEquals(100.00, $report[2]['revenue']);
        $this->assertEquals(1, $report[2]['total_orders']);
    }

    public function test_get_revenue_report_excludes_canceled_orders(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();

        Order::factory()->for($restaurant)->for($customer)->create([
            'total' => 200.00,
            'status' => Order::STATUS_CANCELED,
            'ordered_at' => Carbon::now()->startOfYear()->addMonth(1),
        ]);

        Order::factory()->for($restaurant)->for($customer)->create([
            'total' => 50.00,
            'status' => Order::STATUS_COMPLETED,
            'ordered_at' => Carbon::now()->startOfYear()->addMonth(1),
        ]);

        $report = $this->reportService->getRevenueReport($restaurant->id, 'monthly', Carbon::now()->year);

        $this->assertEquals(50.00, $report[1]['revenue']);
        $this->assertEquals(1, $report[1]['total_orders']);
    }

    public function test_get_best_selling_dishes_returns_ordered_results(): void
    {
        $restaurant = Restaurant::factory()->create();
        $order = Order::factory()->for($restaurant)->create(['status' => Order::STATUS_COMPLETED]);

        OrderItem::factory()->for($order)->create(['dish_name' => 'Feijoada', 'quantity' => 5, 'subtotal' => 100]);
        OrderItem::factory()->for($order)->create(['dish_name' => 'Arroz', 'quantity' => 3, 'subtotal' => 30]);

        $dishes = $this->reportService->getBestSellingDishes($restaurant->id);

        $this->assertCount(2, $dishes);
        $this->assertEquals('Feijoada', $dishes[0]['dish_name']);
        $this->assertEquals(5, $dishes[0]['total_quantity']);
    }

    public function test_get_peak_hours_returns_hourly_distribution(): void
    {
        $restaurant = Restaurant::factory()->create();
        Order::factory()->for($restaurant)->count(3)->create([
            'ordered_at' => Carbon::now()->setHour(12),
            'status' => Order::STATUS_COMPLETED,
        ]);

        $hours = $this->reportService->getPeakHours($restaurant->id);

        $this->assertNotEmpty($hours);
        $this->assertEquals(12, $hours[0]['hour']);
    }

    public function test_get_popular_combinations_returns_paired_dishes(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();

        $order = Order::factory()->for($restaurant)->for($customer)->create([
            'status' => Order::STATUS_COMPLETED,
        ]);

        OrderItem::factory()->for($order)->create(['dish_name' => 'Feijoada', 'order_id' => $order->id]);
        OrderItem::factory()->for($order)->create(['dish_name' => 'Couve', 'order_id' => $order->id]);

        $combos = $this->reportService->getPopularCombinations($restaurant->id);

        $this->assertNotEmpty($combos);
    }

    public function test_reports_return_empty_for_restaurant_without_orders(): void
    {
        $restaurant = Restaurant::factory()->create();

        $report = $this->reportService->getRevenueReport($restaurant->id);
        $dishes = $this->reportService->getBestSellingDishes($restaurant->id);
        $hours = $this->reportService->getPeakHours($restaurant->id);

        $this->assertCount(12, $report);
        foreach ($report as $month) {
            $this->assertEquals(0, $month['total_orders']);
        }
        $this->assertEmpty($dishes);
        $this->assertEmpty($hours);
    }
}
