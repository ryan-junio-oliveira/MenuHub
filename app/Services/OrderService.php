<?php

namespace App\Services;

use App\Events\OrderStatusChanged;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data, int $restaurantId): Order
    {
        return DB::transaction(function () use ($data, $restaurantId) {
            $customerId = $data['customer_id'] ?? null;

            if (!$customerId && isset($data['customer'])) {
                $customer = Customer::firstOrCreate(
                    ['restaurant_id' => $restaurantId, 'phone' => $data['customer']['phone']],
                    [
                        'restaurant_id' => $restaurantId,
                        'name' => $data['customer']['name'],
                        'phone' => $data['customer']['phone'],
                        'address' => $data['customer']['address'] ?? null,
                    ]
                );
                $customerId = $customer->id;
            }

            $orderNumber = $this->generateOrderNumber($restaurantId);

            $order = Order::create([
                'restaurant_id' => $restaurantId,
                'customer_id' => $customerId,
                'order_number' => $orderNumber,
                'status' => Order::STATUS_RECEIVED,
                'source' => $data['source'] ?? 'whatsapp',
                'notes' => $data['notes'] ?? null,
                'customer_notes' => $data['customer_notes'] ?? null,
                'subtotal' => $data['subtotal'],
                'delivery_fee' => $data['delivery_fee'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'total' => $data['total'],
                'payment_method' => $data['payment_method'] ?? null,
                'payment_status' => 'pending',
                'delivery_type' => $data['delivery_type'] ?? 'delivery',
                'delivery_address' => $data['delivery_address'] ?? null,
                'ordered_at' => now(),
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'dish_id' => $item['dish_id'] ?? null,
                    'daily_menu_item_id' => $item['daily_menu_item_id'] ?? null,
                    'dish_name' => $item['dish_name'],
                    'size' => $item['size'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $order;
        });
    }

    public function updateStatus(Order $order, string $newStatus): Order
    {
        $oldStatus = $order->status;

        if (!in_array($newStatus, Order::STATUSES)) {
            throw new \InvalidArgumentException("Invalid status: {$newStatus}");
        }

        $order->update([
            'status' => $newStatus,
            'status_updated_at' => now(),
        ]);

        event(new OrderStatusChanged($order, $oldStatus, $newStatus));

        return $order->fresh();
    }

    public function getOrdersByStatus(int $restaurantId): array
    {
        $orders = [];
        foreach (Order::STATUSES as $status) {
            $orders[$status] = Order::where('restaurant_id', $restaurantId)
                ->where('status', $status)
                ->with(['customer', 'items'])
                ->orderBy('ordered_at', 'desc')
                ->get();
        }
        return $orders;
    }

    private function generateOrderNumber(int $restaurantId): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $lastOrder = Order::where('restaurant_id', $restaurantId)
            ->whereDate('ordered_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;

        return "{$prefix}-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
