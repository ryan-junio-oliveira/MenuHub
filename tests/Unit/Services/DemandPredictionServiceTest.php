<?php

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Services\DemandPredictionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemandPredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    private DemandPredictionService $demandPredictionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->demandPredictionService = app(DemandPredictionService::class);
    }

    public function test_predict_weekly_demand_returns_structure(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();
        $order = Order::factory()->for($restaurant)->for($customer)->create([
            'status' => Order::STATUS_COMPLETED,
            'created_at' => Carbon::now()->subWeek(),
        ]);
        OrderItem::factory()->for($order)->create(['dish_name' => 'Arroz', 'quantity' => 10]);

        $prediction = $this->demandPredictionService->predictWeeklyDemand($restaurant->id);

        $this->assertArrayHasKey('predictions', $prediction);
        $this->assertArrayHasKey('weekly_total_predicted', $prediction);
        $this->assertArrayHasKey('last_week_total', $prediction);
        $this->assertArrayHasKey('trend_pct', $prediction);
        $this->assertArrayHasKey('day_multipliers', $prediction);
    }

    public function test_predict_weekly_demand_returns_prediction_per_dish(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();
        $order = Order::factory()->for($restaurant)->for($customer)->create([
            'status' => Order::STATUS_COMPLETED,
            'created_at' => Carbon::now()->subWeek(),
        ]);
        OrderItem::factory()->for($order)->create(['dish_name' => 'Feijoada', 'quantity' => 7]);

        $prediction = $this->demandPredictionService->predictWeeklyDemand($restaurant->id);

        $this->assertCount(1, $prediction['predictions']);
        $this->assertEquals('Feijoada', $prediction['predictions'][0]['dish_name']);
        $this->assertGreaterThan(0, $prediction['predictions'][0]['predicted_weekly']);
    }

    public function test_predict_weekly_demand_returns_empty_for_no_data(): void
    {
        $restaurant = Restaurant::factory()->create();

        $prediction = $this->demandPredictionService->predictWeeklyDemand($restaurant->id);

        $this->assertEmpty($prediction['predictions']);
        $this->assertEquals(0, $prediction['weekly_total_predicted']);
    }

    public function test_day_multipliers_returns_7_days(): void
    {
        $restaurant = Restaurant::factory()->create();

        $prediction = $this->demandPredictionService->predictWeeklyDemand($restaurant->id);

        $this->assertCount(7, $prediction['day_multipliers']);
    }
}
