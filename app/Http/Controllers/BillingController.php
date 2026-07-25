<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Restaurant;
use App\Services\PaymentGatewayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentGateway
    ) {}

    public function index(Request $request)
    {
        $restaurants = Restaurant::with('plan')
            ->withCount(['invoices as pending_invoices' => function ($q) {
                $q->where('status', 'pending');
            }])
            ->orderBy('name')
            ->paginate(20);

        $stats = [
            'total' => Restaurant::count(),
            'active' => Restaurant::where('subscription_status', 'active')->count(),
            'trial' => Restaurant::where('subscription_status', 'trial')->count(),
            'expired' => Restaurant::where('subscription_status', 'expired')->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'monthly_revenue' => Invoice::where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        return view('billing.index', compact('restaurants', 'stats'));
    }

    public function restaurantBilling(Restaurant $restaurant)
    {
        $restaurant->load('plan', 'invoices');
        return view('billing.restaurant', compact('restaurant'));
    }

    public function generateInvoice(Request $request, Restaurant $restaurant)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'due_date' => 'required|date|after:today',
            'plan_id' => 'nullable|exists:plans,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $invoice = DB::transaction(function () use ($restaurant, $validated) {
            $invoice = Invoice::create([
                'restaurant_id' => $restaurant->id,
                'plan_id' => $validated['plan_id'] ?? $restaurant->plan_id,
                'amount' => $validated['amount'],
                'status' => 'pending',
                'due_date' => $validated['due_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            try {
                $result = $this->paymentGateway->charge([
                    'amount' => $invoice->amount,
                    'order_id' => 'BILL-' . $invoice->id,
                    'pix_key' => $restaurant->pix_key,
                    'email' => $restaurant->email,
                    'customer_name' => $restaurant->name,
                    'description' => "Assinatura MenuHub - {$restaurant->name}",
                ]);

                $invoice->update([
                    'pix_qr_code' => $result['pix_qr_code'] ?? null,
                    'pix_copy_paste' => $result['pix_code'] ?? null,
                    'transaction_id' => $result['transaction_id'] ?? null,
                ]);
            } catch (\Exception $e) {
                $invoice->update([
                    'notes' => ($invoice->notes ? $invoice->notes . "\n" : '') . 'Erro ao gerar PIX: ' . $e->getMessage(),
                ]);
            }

            return $invoice;
        });

        return redirect()->route('root.billing.restaurant', $restaurant)
            ->with('success', 'Cobrança gerada com sucesso.');
    }

    public function confirmPayment(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $restaurant = $invoice->restaurant;
        $paidUntil = $restaurant->paid_until
            ? max(Carbon::parse($restaurant->paid_until), now())->addMonth()
            : now()->addMonth();

        $restaurant->update([
            'subscription_status' => 'active',
            'paid_until' => $paidUntil,
        ]);

        return back()->with('success', 'Pagamento confirmado. Assinatura ativa até ' . $paidUntil->format('d/m/Y') . '.');
    }

    public function markOverdue(Invoice $invoice)
    {
        $invoice->update(['status' => 'overdue']);

        $restaurant = $invoice->restaurant;
        if ($restaurant->paid_until && $restaurant->paid_until->isPast()) {
            $restaurant->update(['subscription_status' => 'expired']);
        }

        return back()->with('success', 'Cobrança marcada como vencida.');
    }

    public function cancelInvoice(Invoice $invoice)
    {
        if ($invoice->status !== 'pending') {
            return back()->with('error', 'Apenas cobranças pendentes podem ser canceladas.');
        }

        $invoice->update(['status' => 'canceled']);
        return back()->with('success', 'Cobrança cancelada.');
    }

    public function plans()
    {
        $plans = Plan::all();
        return view('billing.plans', compact('plans'));
    }

    public function updatePlan(Request $request, Restaurant $restaurant)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $restaurant->update(['plan_id' => $validated['plan_id']]);

        return back()->with('success', 'Plano do restaurante atualizado.');
    }

    public function updateSubscriptionStatus(Request $request, Restaurant $restaurant)
    {
        $validated = $request->validate([
            'subscription_status' => 'required|in:trial,active,canceled,expired',
            'paid_until' => 'nullable|date',
            'trial_ends_at' => 'nullable|date',
        ]);

        $restaurant->update($validated);

        return back()->with('success', 'Status da assinatura atualizado.');
    }
}
