<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Models\Customer;

class UpdateCustomerStats
{
    public function handle(OrderStatusChanged $event): void
    {
        if (!$event->order->customer_id) {
            return;
        }

        $customer = Customer::find($event->order->customer_id);
        if (!$customer) {
            return;
        }

        if ($event->newStatus === \App\Models\Order::STATUS_COMPLETED) {
            $customer->increment('total_orders');
            $customer->increment('total_spent', $event->order->total);
        }
    }
}
