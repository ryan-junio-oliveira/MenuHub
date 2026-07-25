<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Contracts\WhatsAppInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $type
    ) {}

    public function handle(WhatsAppInterface $whatsApp): void
    {
        $customer = $this->order->customer;
        $restaurant = $this->order->restaurant;

        if (!$customer || !$customer->phone || !$restaurant) {
            return;
        }

        if (!$restaurant->whatsapp_phone_id || !$restaurant->whatsapp_api_token) {
            return;
        }

        $phone = $this->normalizePhone($customer->phone);
        if (!$phone) {
            return;
        }

        switch ($this->type) {
            case 'order_confirmation':
                $orderData = [
                    'order_number' => $this->order->order_number,
                    'items' => $this->order->items->map(fn($item) => [
                        'dish_name' => $item->dish_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'size' => $item->size,
                    ])->toArray(),
                    'subtotal' => $this->order->subtotal,
                    'delivery_fee' => $this->order->delivery_fee,
                    'total' => $this->order->total,
                    'delivery_type' => $this->order->delivery_type,
                    'payment_method' => $this->order->payment_method,
                    'estimated_time' => 40,
                ];

                $whatsApp->sendOrderConfirmation($phone, $orderData, $restaurant);

                if ($this->order->payment_method === 'pix' && !empty($restaurant->pix_key)) {
                    $whatsApp->sendPixPayment(
                        $phone,
                        $restaurant->pix_key,
                        $this->order->total,
                        $this->order->order_number,
                        $restaurant
                    );
                }
                break;

            case 'status_update':
                $statusDetails = match ($this->order->status) {
                    'preparing' => 'Seu pedido está sendo preparado com carinho! 👨‍🍳',
                    'out_for_delivery' => 'Seu pedido saiu para entrega! 🛵',
                    'completed' => 'Pedido finalizado! Bom apetite! 🍽️',
                    'canceled' => 'Pedido cancelado.',
                    default => null,
                };

                $whatsApp->sendStatusUpdate(
                    $phone,
                    $this->order->order_number,
                    $this->order->status,
                    $statusDetails,
                    $restaurant
                );
                break;

            case 'menu_reminder':
                $todayMenu = \App\Models\DailyMenu::where('restaurant_id', $restaurant->id)
                    ->where('menu_date', today())
                    ->where('is_published', true)
                    ->first();

                if ($todayMenu) {
                    $whatsApp->sendMessage(
                        $phone,
                        "Olá! O cardápio de hoje já está disponível! 🍽️\n\n"
                            . "Peça já pelo WhatsApp! É rápido e fácil. 😊",
                        $restaurant
                    );
                }
                break;
        }
    }

    private function normalizePhone(string $phone): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) < 10 || strlen($phone) > 13) return null;
        if (strlen($phone) === 10 || strlen($phone) === 11) $phone = '55' . $phone;
        return $phone;
    }
}
