<?php

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Services\ThermalPrinterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThermalPrinterServiceTest extends TestCase
{
    use RefreshDatabase;

    private ThermalPrinterService $printerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->printerService = new ThermalPrinterService();
    }

    public function test_format_order_58mm_returns_string(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();
        $order = Order::factory()->for($restaurant)->for($customer)->create();
        OrderItem::factory()->for($order)->create(['dish_name' => 'Feijoada', 'quantity' => 2, 'unit_price' => 25.00]);

        $output = $this->printerService->formatOrder($order, '58mm');

        $this->assertIsString($output);
        $this->assertStringContainsString('COMANDA DE COZINHA', $output);
        $this->assertStringContainsString($order->order_number, $output);
        $this->assertStringContainsString('Feijoada', $output);
        $this->assertStringContainsString('MenuHub', $output);
    }

    public function test_format_order_80mm_returns_string(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();
        $order = Order::factory()->for($restaurant)->for($customer)->create(['delivery_type' => 'delivery']);
        OrderItem::factory()->for($order)->create(['dish_name' => 'Prato Teste', 'quantity' => 1, 'unit_price' => 30.00]);

        $output = $this->printerService->formatOrder($order, '80mm');

        $this->assertIsString($output);
        $this->assertStringContainsString('ENTREGA', $output);
        $this->assertStringContainsString('Prato Teste', $output);
    }

    public function test_format_order_shows_pickup_when_not_delivery(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();
        $order = Order::factory()->for($restaurant)->for($customer)->create(['delivery_type' => 'pickup']);

        $output = $this->printerService->formatOrder($order);

        $this->assertStringContainsString('RETIRADA', $output);
    }

    public function test_format_order_shows_customer_notes(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();
        $order = Order::factory()->for($restaurant)->for($customer)->create();
        $order->customer_notes = 'Sem cebola, por favor';
        $order->save();

        $output = $this->printerService->formatOrder($order);

        $this->assertStringContainsString('Sem cebola', $output);
    }

    public function test_format_order_includes_payment_method(): void
    {
        $restaurant = Restaurant::factory()->create(['pix_key' => 'test@example.com']);
        $customer = Customer::factory()->for($restaurant)->create();
        $order = Order::factory()->for($restaurant)->for($customer)->create(['payment_method' => 'pix']);

        $output = $this->printerService->formatOrder($order);

        $this->assertStringContainsString('PIX', $output);
    }

    public function test_get_status_returns_online(): void
    {
        $this->assertEquals('online', $this->printerService->getStatus());
    }
}
