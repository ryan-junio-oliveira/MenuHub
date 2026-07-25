@extends('layouts.app')
@section('title', __('Pagamentos'))
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Pagamentos') }}</h1>
        <p class="page-subtitle">{{ __('Histórico de pagamentos dos pedidos') }}</p>
    </div>

    <x-card padding="0">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">{{ __('Pedido') }}</th>
                        <th class="table-th">{{ __('Cliente') }}</th>
                        <th class="table-th">{{ __('Método') }}</th>
                        <th class="table-th">{{ __('Valor') }}</th>
                        <th class="table-th">{{ __('Status') }}</th>
                        <th class="table-th">{{ __('Data') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @forelse ($payments as $payment)
                    <tr class="table-row">
                        <td class="table-td font-medium">#{{ $payment->order?->order_number ?? $payment->order_id }}</td>
                        <td class="table-td">{{ $payment->order?->customer?->name ?? '-' }}</td>
                        <td class="table-td">
                            @php
                            $methodLabels = ['pix' => 'PIX', 'credit_card' => 'Cartão Crédito', 'debit_card' => 'Cartão Débito', 'cash' => 'Dinheiro'];
                            $methodIcons = ['pix' => 'fa-solid fa-qrcode', 'credit_card' => 'fa-regular fa-credit-card', 'debit_card' => 'fa-regular fa-credit-card', 'cash' => 'fa-solid fa-money-bill'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 text-sm">
                                <i class="{{ $methodIcons[$payment->method] ?? 'fa-regular fa-circle' }} text-text-secondary"></i>
                                {{ $methodLabels[$payment->method] ?? $payment->method }}
                            </span>
                        </td>
                        <td class="table-td font-medium">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                        <td class="table-td">
                            @php
                            $statusClasses = ['pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300', 'completed' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300', 'failed' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300', 'refunded' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'];
                            $statusLabels = ['pending' => 'Pendente', 'completed' => 'Confirmado', 'failed' => 'Falha', 'refunded' => 'Reembolsado'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$payment->status] ?? 'bg-slate-100' }}">
                                {{ $statusLabels[$payment->status] ?? $payment->status }}
                            </span>
                        </td>
                        <td class="table-td text-sm text-text-secondary">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="table-td text-center py-12 text-text-secondary">
                            <i class="fa-regular fa-credit-card text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                            {{ __('Nenhum pagamento registrado.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

@endsection
