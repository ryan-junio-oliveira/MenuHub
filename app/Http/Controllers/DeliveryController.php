<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorizePlanFeature($request, 'delivery_management');
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $deliveries = Delivery::where('restaurant_id', $restaurantId)
            ->with('order.customer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('deliveries.index', compact('deliveries'));
    }

    public function create(Request $request, ?Order $order = null)
    {
        $restaurantId = $request->user()->restaurant_id;

        $orders = Order::where('restaurant_id', $restaurantId)
            ->where('delivery_type', 'delivery')
            ->whereDoesntHave('delivery')
            ->with('customer')
            ->get();

        return view('deliveries.form', ['delivery' => null, 'orders' => $orders, 'selectedOrder' => $order]);
    }

    public function store(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'type' => ['required', 'string', 'in:delivery,pickup'],
            'address' => ['nullable', 'string'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'in:pending,in_transit,delivered,failed'],
            'estimated_delivery_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        Delivery::create([
            ...$validated,
            'restaurant_id' => $restaurantId,
        ]);

        return redirect()->route('deliveries.index')->with('success', 'Entrega cadastrada com sucesso!');
    }

    public function show(Delivery $delivery)
    {
        $delivery->load('order.customer', 'order.items');
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        return view('deliveries.form', ['delivery' => $delivery, 'orders' => collect(), 'selectedOrder' => null]);
    }

    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'address' => ['nullable', 'string'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'in:pending,in_transit,delivered,failed'],
            'estimated_delivery_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['status'] === 'delivered' && !$delivery->delivered_at) {
            $validated['delivered_at'] = now();
        }

        $delivery->update($validated);

        if ($validated['status'] === 'delivered') {
            $delivery->order->update(['status' => 'completed']);
        }

        return redirect()->route('deliveries.show', $delivery)->with('success', 'Entrega atualizada com sucesso!');
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();
        return redirect()->route('deliveries.index')->with('success', 'Entrega excluída com sucesso!');
    }
}
