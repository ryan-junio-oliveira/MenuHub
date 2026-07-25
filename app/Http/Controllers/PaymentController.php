<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $payments = Payment::where('restaurant_id', $restaurantId)
            ->with('order.customer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load('order.customer', 'order.items');
        return view('payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,completed,failed,refunded'],
            'transaction_id' => ['nullable', 'string'],
        ]);

        $payment->update($validated);

        if ($validated['status'] === 'completed') {
            $payment->order->update(['payment_status' => 'paid']);
        }

        return redirect()->back()->with('success', 'Pagamento atualizado com sucesso!');
    }
}
