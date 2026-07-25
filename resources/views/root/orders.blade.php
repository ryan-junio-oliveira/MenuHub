@extends('layouts.app')
@section('title', __('Pedidos Globais'))
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Pedidos Globais') }}</h1>
        <p class="page-subtitle">{{ __('Todos os pedidos de todos os restaurantes') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <p class="stat-label">{{ __('Total de Pedidos') }}</p>
            <p class="stat-value">{{ $totalOrders }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">{{ __('Pendentes') }}</p>
            <p class="stat-value text-amber-600">{{ $pendingOrders }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">{{ __('Receita Total') }}</p>
            <p class="stat-value text-green-600">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
        </div>
    </div>

    <x-card padding="0">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">{{ __('Pedido') }}</th>
                        <th class="table-th">{{ __('Restaurante') }}</th>
                        <th class="table-th">{{ __('Cliente') }}</th>
                        <th class="table-th">{{ __('Total') }}</th>
                        <th class="table-th">{{ __('Pagamento') }}</th>
                        <th class="table-th">{{ __('Status') }}</th>
                        <th class="table-th">{{ __('Data') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @forelse ($orders as $order)
                    <tr class="table-row">
                        <td class="table-td font-medium">#{{ $order->order_number ?? $order->id }}</td>
                        <td class="table-td">
                            <span class="inline-flex items-center gap-1.5 text-sm">
                                <i class="fa-solid fa-store text-xs text-text-secondary"></i>
                                {{ $order->restaurant?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="table-td">{{ $order->customer?->name ?? 'Avulso' }}</td>
                        <td class="table-td font-medium">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                        <td class="table-td text-sm text-text-secondary">{{ ['pix' => 'PIX', 'cash' => 'Dinheiro', 'credit_card' => 'Cartão', 'debit_card' => 'Débito'][$order->payment_method] ?? $order->payment_method }}</td>
                        <td class="table-td"><x-status-badge :status="$order->status" /></td>
                        <td class="table-td text-sm text-text-secondary">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="table-td text-center py-12 text-text-secondary">
                            <i class="fa-solid fa-cart-shopping text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                            {{ __('Nenhum pedido encontrado.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($orders, 'links'))
        <div class="p-4 border-t border-border dark:border-border-dark">{{ $orders->links() }}</div>
        @endif
    </x-card>
</div>

@endsection
