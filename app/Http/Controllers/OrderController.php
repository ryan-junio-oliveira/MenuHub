<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\DailyMenu;
use App\Models\Customer;
use App\Models\Dish;
use App\Models\Order;
use App\Services\DailyMenuService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly DailyMenuService $dailyMenuService,
    ) {}

    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $query = Order::where('restaurant_id', $restaurantId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->withCount('items')->latest()->paginate(20);

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $customers = Customer::where('restaurant_id', $restaurantId)->get();
        $todayMenu = DailyMenu::where('restaurant_id', $restaurantId)
            ->where('menu_date', today())
            ->where('is_published', true)
            ->with('items.dish')
            ->first();

        $menuItems = $todayMenu?->items ?? collect();

        return view('orders.create', compact('customers', 'menuItems', 'todayMenu'));
    }

    public function store(StoreOrderRequest $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $data = $request->validated();

        if (isset($data['items'])) {
            $data['items'] = array_values($data['items']);
            $data['items'] = array_filter($data['items'], fn($item) => ($item['quantity'] ?? 0) > 0);
            $data['items'] = array_values($data['items']);
        }

        try {
            $order = $this->orderService->createOrder($data, $restaurantId);
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $order)->with('success', 'Pedido criado com sucesso!');
    }

    public function show(Order $order)
    {
        $order->load('items.dish', 'customer', 'restaurant');

        return view('orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $this->orderService->updateStatus(
            $order,
            $request->validated()['status'],
        );

        return redirect()->route('orders.index');
    }

    public function kanban(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $orders = $this->orderService->getOrdersByStatus($restaurantId);

        return view('orders.kanban', compact('orders'));
    }

    public function globalIndex(Request $request)
    {
        $orders = Order::with('customer', 'restaurant')
            ->latest()
            ->paginate(30);

        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $totalOrders = Order::count();
        $pendingOrders = Order::whereIn('status', ['pending', 'received', 'preparing'])->count();

        return view('root.orders', compact('orders', 'totalRevenue', 'totalOrders', 'pendingOrders'));
    }

    public function edit(Request $request, Order $order)
    {
        $order->load('items', 'customer');

        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', 'string', 'in:pix,cash,credit_card,debit_card'],
            'delivery_type' => ['required', 'string', 'in:delivery,pickup'],
            'delivery_address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'items' => ['nullable', 'array'],
            'items.*.id' => ['required', 'exists:order_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
            'items.*.dish_name' => ['required', 'string'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.size' => ['nullable', 'string'],
        ]);

        $order->update([
            'customer_id' => $validated['customer_id'] ?: null,
            'payment_method' => $validated['payment_method'],
            'delivery_type' => $validated['delivery_type'],
            'delivery_address' => $validated['delivery_address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'subtotal' => $validated['subtotal'],
            'delivery_fee' => $validated['delivery_fee'] ?? 0,
            'discount' => $validated['discount'] ?? 0,
            'total' => $validated['total'],
        ]);

        if (isset($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                $item = $order->items()->find($itemData['id']);
                if (!$item) continue;

                if (($itemData['quantity'] ?? 0) <= 0) {
                    $item->delete();
                } else {
                    $item->update([
                        'quantity' => $itemData['quantity'],
                        'dish_name' => $itemData['dish_name'],
                        'unit_price' => $itemData['unit_price'],
                        'size' => $itemData['size'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('orders.show', $order)->with('success', 'Pedido atualizado com sucesso!');
    }

    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->delete();
        });

        return redirect()->route('orders.index')->with('success', 'Pedido excluído com sucesso!');
    }
}
