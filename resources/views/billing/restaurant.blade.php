@extends('layouts.app')
@section('title', 'Cobrança - ' . $restaurant->name)
@section('content')

<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('root.billing.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i> Voltar às Cobranças
        </a>
    </div>

    <div class="page-header flex items-center justify-between">
        <div class="flex items-center gap-4">
            <h1 class="page-title">{{ $restaurant->name }}</h1>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                {{ $restaurant->subscription_status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : '' }}
                {{ $restaurant->subscription_status === 'trial' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                {{ $restaurant->subscription_status === 'expired' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                {{ $restaurant->subscription_status === 'canceled' ? 'bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-300' : '' }}">
                {{ ucfirst($restaurant->subscription_status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2">
            <x-card padding="5" class="mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">Informações da Assinatura</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">Plano</p>
                        <p class="text-sm font-medium">{{ $restaurant->plan?->name ?? 'Nenhum' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">Valor</p>
                        <p class="text-sm font-medium">R$ {{ number_format($restaurant->plan?->price ?? 0, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">Trial até</p>
                        <p class="text-sm font-medium">{{ $restaurant->trial_ends_at?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">Pago até</p>
                        <p class="text-sm font-medium">{{ $restaurant->paid_until?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-border dark:border-border-dark">
                    <h4 class="text-sm font-semibold text-text-primary dark:text-text-dark mb-3">Atualizar Status</h4>
                    <form method="POST" action="{{ route('root.billing.update-status', $restaurant) }}" class="flex items-end gap-3">
                        @csrf @method('PUT')
                        <div>
                            <label class="text-xs text-text-secondary block mb-1">Status</label>
                            <select name="subscription_status" class="input-field text-sm">
                                <option value="trial" @selected($restaurant->subscription_status === 'trial')>Trial</option>
                                <option value="active" @selected($restaurant->subscription_status === 'active')>Ativa</option>
                                <option value="expired" @selected($restaurant->subscription_status === 'expired')>Expirada</option>
                                <option value="canceled" @selected($restaurant->subscription_status === 'canceled')>Cancelada</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-text-secondary block mb-1">Pago até</label>
                            <input type="date" name="paid_until" value="{{ $restaurant->paid_until?->format('Y-m-d') }}" class="input-field text-sm">
                        </div>
                        <button type="submit" class="btn-primary text-sm">Salvar</button>
                    </form>
                </div>
            </x-card>

            <x-card padding="5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark">Histórico de Cobranças</h3>
                    <button type="button" onclick="document.getElementById('generateInvoiceModal').classList.remove('hidden')"
                            class="btn-primary text-sm">
                        <i class="fa-solid fa-plus text-xs"></i> Gerar Cobrança
                    </button>
                </div>

                @if ($restaurant->invoices->isEmpty())
                    <p class="text-center py-8 text-text-secondary text-sm">Nenhuma cobrança gerada.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-th">#</th>
                                    <th class="table-th">Valor</th>
                                    <th class="table-th">Vencimento</th>
                                    <th class="table-th">Status</th>
                                    <th class="table-th">Pagamento</th>
                                    <th class="table-th">PIX</th>
                                    <th class="table-th"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border dark:divide-border-dark">
                                @foreach ($restaurant->invoices->sortByDesc('created_at') as $invoice)
                                <tr class="table-row">
                                    <td class="table-td font-mono text-xs">#{{ $invoice->id }}</td>
                                    <td class="table-td font-medium">R$ {{ number_format($invoice->amount, 2, ',', '.') }}</td>
                                    <td class="table-td text-sm">{{ $invoice->due_date->format('d/m/Y') }}</td>
                                    <td class="table-td">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                                'paid' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                                'overdue' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                                'canceled' => 'bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-300',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$invoice->status] }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="table-td text-sm">{{ $invoice->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="table-td">
                                        @if ($invoice->pix_copy_paste)
                                        <button type="button" onclick="showPix('{{ $invoice->id }}')"
                                                class="text-xs font-medium text-primary-600 hover:text-primary-500">
                                            Ver PIX
                                        </button>
                                        <div id="pixModal{{ $invoice->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" onclick="this.classList.add('hidden')">
                                            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl" onclick="event.stopPropagation()">
                                                <div class="flex items-center justify-between mb-4">
                                                    <h4 class="text-base font-semibold">PIX - R$ {{ number_format($invoice->amount, 2, ',', '.') }}</h4>
                                                    <button type="button" onclick="document.getElementById('pixModal{{ $invoice->id }}').classList.add('hidden')" class="text-text-secondary hover:text-text-primary">
                                                        <i class="fa-solid fa-xmark text-xl"></i>
                                                    </button>
                                                </div>
                                                @if ($invoice->pix_qr_code)
                                                <div class="flex justify-center mb-4">
                                                    <img src="data:image/png;base64,{{ $invoice->pix_qr_code }}" alt="QR Code PIX" class="w-48 h-48">
                                                </div>
                                                @endif
                                                <div class="mb-4">
                                                    <label class="text-xs text-text-secondary block mb-1">Código PIX</label>
                                                    <div class="flex gap-2">
                                                        <input type="text" readonly value="{{ $invoice->pix_copy_paste }}" class="input-field text-xs flex-1 font-mono" id="pixCode{{ $invoice->id }}">
                                                        <button type="button" onclick="copyPix('{{ $invoice->id }}')" class="btn-secondary text-sm shrink-0">
                                                            <i class="fa-regular fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-text-secondary">Vencimento: {{ $invoice->due_date->format('d/m/Y') }}</p>
                                            </div>
                                        </div>
                                        @else
                                        <span class="text-xs text-text-secondary">Indisponível</span>
                                        @endif
                                    </td>
                                    <td class="table-td text-right">
                                        @if ($invoice->status === 'pending')
                                        <div class="flex items-center gap-1">
                                            <form method="POST" action="{{ route('root.billing.confirm-payment', $invoice) }}" class="inline">
                                                @csrf @method('PUT')
                                                <button type="submit" class="text-xs font-medium text-green-600 hover:text-green-500" onclick="return confirm('Confirmar pagamento desta cobrança?')">
                                                    Confirmar
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('root.billing.mark-overdue', $invoice) }}" class="inline">
                                                @csrf @method('PUT')
                                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-500 ml-2">
                                                    Vencido
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('root.billing.cancel-invoice', $invoice) }}" class="inline">
                                                @csrf @method('PUT')
                                                <button type="submit" class="text-xs font-medium text-slate-500 hover:text-slate-400 ml-2" onclick="return confirm('Cancelar esta cobrança?')">
                                                    Cancelar
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>

        <div>
            <x-card padding="5" class="mb-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-3">Resumo</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">Total cobrado</span>
                        <span class="font-medium">R$ {{ number_format($restaurant->invoices->sum('amount'), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">Total pago</span>
                        <span class="font-medium text-green-600">R$ {{ number_format($restaurant->invoices->where('status', 'paid')->sum('amount'), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">Total pendente</span>
                        <span class="font-medium text-amber-600">R$ {{ number_format($restaurant->invoices->where('status', 'pending')->sum('amount'), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm pt-2 border-t border-border dark:border-border-dark">
                        <span class="text-text-secondary">Faturas</span>
                        <span class="font-medium">{{ $restaurant->invoices->count() }}</span>
                    </div>
                </div>
            </x-card>

            <x-card padding="5">
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-3">Alterar Plano</h3>
                <form method="POST" action="{{ route('root.billing.update-plan', $restaurant) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <select name="plan_id" class="input-field text-sm">
                            @foreach (\App\Models\Plan::all() as $plan)
                            <option value="{{ $plan->id }}" @selected($restaurant->plan_id === $plan->id)>
                                {{ $plan->name }} - R$ {{ number_format($plan->price, 2, ',', '.') }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary text-sm w-full">Atualizar Plano</button>
                </form>
            </x-card>
        </div>
    </div>
</div>

{{-- Generate Invoice Modal --}}
<div id="generateInvoiceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-base font-semibold">Gerar Cobrança</h4>
            <button type="button" onclick="document.getElementById('generateInvoiceModal').classList.add('hidden')" class="text-text-secondary hover:text-text-primary">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('root.billing.generate-invoice', $restaurant) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="text-xs text-text-secondary block mb-1">Valor (R$)</label>
                    <input type="number" name="amount" step="0.01" min="1" value="{{ $restaurant->plan?->price ?? 49 }}"
                           class="input-field text-sm" required>
                </div>
                <div>
                    <label class="text-xs text-text-secondary block mb-1">Plano (opcional)</label>
                    <select name="plan_id" class="input-field text-sm">
                        <option value="">Sem plano</option>
                        @foreach (\App\Models\Plan::all() as $plan)
                        <option value="{{ $plan->id }}" @selected($restaurant->plan_id === $plan->id)>
                            {{ $plan->name }} - R$ {{ number_format($plan->price, 2, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-text-secondary block mb-1">Data de Vencimento</label>
                    <input type="date" name="due_date" value="{{ now()->addDays(7)->format('Y-m-d') }}"
                           class="input-field text-sm" required>
                </div>
                <div>
                    <label class="text-xs text-text-secondary block mb-1">Observações (opcional)</label>
                    <textarea name="notes" rows="2" class="input-field text-sm" placeholder="Observações internas..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex items-center gap-3 justify-end">
                <button type="button" onclick="document.getElementById('generateInvoiceModal').classList.add('hidden')"
                        class="btn-secondary text-sm">Cancelar</button>
                <button type="submit" class="btn-primary text-sm">
                    <i class="fa-solid fa-bolt text-xs"></i> Gerar e Emitir PIX
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showPix(id) {
    document.getElementById('pixModal' + id).classList.remove('hidden');
}
function copyPix(id) {
    const input = document.getElementById('pixCode' + id);
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = input.nextElementSibling;
        btn.innerHTML = '<i class="fa-regular fa-check"></i>';
        setTimeout(() => btn.innerHTML = '<i class="fa-regular fa-copy"></i>', 2000);
    });
}
</script>
@endpush

@endsection
