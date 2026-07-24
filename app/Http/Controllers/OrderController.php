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

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(20);

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

        $order = $this->orderService->createOrder(
            $request->validated(),
            $restaurantId,
        );

        return redirect()->route('orders.show', $order);
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
}
