<?php

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = app(OrderService::class);
    }

    public function test_can_create_order_with_items(): void
    {
        $restaurant = Restaurant::factory()->create();
        $customer = Customer::factory()->for($restaurant)->create();

        $data = [
            'customer_id' => $customer->id,
            'source' => 'whatsapp',
            'subtotal' => 50.00,
            'delivery_fee' => 5.00,
            'total' => 55.00,
            'payment_method' => 'pix',
            'delivery_type' => 'delivery',
            'delivery_address' => 'Rua Teste, 123',
            'items' => [
                [
                    'dish_name' => 'Frango Grelhado',
                    'size' => 'medium',
                    'quantity' => 2,
                    'unit_price' => 25.00,
                    'subtotal' => 50.00,
                ],
            ],
        ];

        $order = $this->orderService->createOrder($data, $restaurant->id);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('received', $order->status);
        $this->assertEquals(55.00, $order->total);
        $this->assertEquals($customer->id, $order->customer_id);
        $this->assertCount(1, $order->items);
        $this->assertEquals('Frango Grelhado', $order->items->first()->dish_name);
    }

    public function test_creates_customer_when_not_provided(): void
    {
        $restaurant = Restaurant::factory()->create();

        $data = [
            'customer' => [
                'name' => 'Novo Cliente',
                'phone' => '(11) 99999-8888',
                'address' => 'Av. Teste, 456',
            ],
            'source' => 'whatsapp',
            'subtotal' => 30.00,
            'total' => 30.00,
            'items' => [
                ['dish_name' => 'Salada', 'quantity' => 1, 'unit_price' => 30.00, 'subtotal' => 30.00],
            ],
        ];

        $order = $this->orderService->createOrder($data, $restaurant->id);

        $this->assertNotNull($order->customer_id);
        $this->assertEquals('Novo Cliente', $order->customer->name);
        $this->assertEquals('(11) 99999-8888', $order->customer->phone);
    }

    public function test_generates_unique_order_numbers(): void
    {
        $restaurant = Restaurant::factory()->create();

        $data = [
            'customer' => ['name' => 'Test', 'phone' => '11111'],
            'subtotal' => 10, 'total' => 10,
            'items' => [['dish_name' => 'Item', 'quantity' => 1, 'unit_price' => 10, 'subtotal' => 10]],
        ];

        $order1 = $this->orderService->createOrder($data, $restaurant->id);
        $order2 = $this->orderService->createOrder($data, $restaurant->id);

        $this->assertNotEquals($order1->order_number, $order2->order_number);
        $this->assertStringContainsString('ORD-', $order1->order_number);
    }

    public function test_update_status_changes_order_status(): void
    {
        $order = Order::factory()->create(['status' => Order::STATUS_RECEIVED]);

        $this->orderService->updateStatus($order, Order::STATUS_PREPARING);

        $order->refresh();
        $this->assertEquals(Order::STATUS_PREPARING, $order->status);
        $this->assertNotNull($order->status_updated_at);
    }

    public function test_update_status_throws_exception_for_invalid_status(): void
    {
        $order = Order::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->orderService->updateStatus($order, 'invalid_status');
    }

    public function test_get_orders_by_status_returns_grouped_orders(): void
    {
        $restaurant = Restaurant::factory()->create();
        Order::factory()->for($restaurant)->count(3)->create(['status' => Order::STATUS_RECEIVED]);
        Order::factory()->for($restaurant)->count(2)->create(['status' => Order::STATUS_PREPARING]);

        $result = $this->orderService->getOrdersByStatus($restaurant->id);

        $this->assertCount(3, $result[Order::STATUS_RECEIVED]);
        $this->assertCount(2, $result[Order::STATUS_PREPARING]);
    }

    public function test_create_order_sets_pending_payment_status(): void
    {
        $restaurant = Restaurant::factory()->create();

        $data = [
            'customer' => ['name' => 'Test', 'phone' => '22222'],
            'subtotal' => 20, 'total' => 20, 'payment_method' => 'credit_card',
            'items' => [['dish_name' => 'Item', 'quantity' => 1, 'unit_price' => 20, 'subtotal' => 20]],
        ];

        $order = $this->orderService->createOrder($data, $restaurant->id);

        $this->assertEquals('pending', $order->payment_status);
    }

    public function test_order_status_transitions_through_all_statuses(): void
    {
        $restaurant = Restaurant::factory()->create();
        $data = [
            'customer' => ['name' => 'Test', 'phone' => '33333'],
            'subtotal' => 15, 'total' => 15,
            'items' => [['dish_name' => 'Item', 'quantity' => 1, 'unit_price' => 15, 'subtotal' => 15]],
        ];

        $order = $this->orderService->createOrder($data, $restaurant->id);
        $this->assertEquals(Order::STATUS_RECEIVED, $order->status);

        foreach ([Order::STATUS_PREPARING, Order::STATUS_OUT_FOR_DELIVERY, Order::STATUS_COMPLETED] as $status) {
            $this->orderService->updateStatus($order, $status);
            $order->refresh();
            $this->assertEquals($status, $order->status);
        }
    }
}
