<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/orders/active', function (Request $request) {
        $restaurantId = $request->user()->restaurant_id;
        $orders = \App\Models\Order::where('restaurant_id', $restaurantId)
            ->whereIn('status', ['received', 'preparing', 'out_for_delivery'])
            ->with(['customer', 'items'])
            ->orderBy('ordered_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer' => $order->customer?->name ?? 'Walk-in',
                    'items' => $order->items->map(fn($item) => [
                        'id' => $item->id,
                        'name' => $item->dish_name,
                        'quantity' => $item->quantity,
                        'size' => $item->size,
                    ]),
                    'total' => (float) $order->total,
                    'status' => $order->status,
                    'created_at' => $order->ordered_at?->toISOString() ?? $order->created_at->toISOString(),
                    'is_urgent' => $order->ordered_at && $order->ordered_at->diffInMinutes(now()) > 30,
                ];
            });

        return response()->json($orders);
    })->name('api.orders.active');
});
