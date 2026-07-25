@extends('layouts.app')
@section('title', __('Entregas'))
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ __('Entregas') }}</h1>
            <p class="page-subtitle">{{ __('Gerenciar entregas dos pedidos') }}</p>
        </div>
        <a href="{{ route('deliveries.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus text-sm"></i>
            {{ __('Nova Entrega') }}
        </a>
    </div>

    <x-card padding="0">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">{{ __('Pedido') }}</th>
                        <th class="table-th">{{ __('Cliente') }}</th>
                        <th class="table-th">{{ __('Endereço') }}</th>
                        <th class="table-th">{{ __('Status') }}</th>
                        <th class="table-th">{{ __('Previsão') }}</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @forelse ($deliveries as $delivery)
                    <tr class="table-row">
                        <td class="table-td font-medium">#{{ $delivery->order->order_number ?? $delivery->order_id }}</td>
                        <td class="table-td">{{ $delivery->contact_name ?? $delivery->order?->customer?->name ?? '-' }}</td>
                        <td class="table-td text-text-secondary text-sm max-w-xs truncate">{{ $delivery->address ?? '-' }}</td>
                        <td class="table-td">
                            @php
                            $statusClasses = ['pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300', 'in_transit' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300', 'delivered' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300', 'failed' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300'];
                            $statusLabels = ['pending' => 'Pendente', 'in_transit' => 'Em Trânsito', 'delivered' => 'Entregue', 'failed' => 'Falha'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$delivery->status] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $statusLabels[$delivery->status] ?? $delivery->status }}
                            </span>
                        </td>
                        <td class="table-td text-sm text-text-secondary">{{ $delivery->estimated_delivery_at ? $delivery->estimated_delivery_at->format('d/m H:i') : '-' }}</td>
                        <td class="table-td text-right">
                            <a href="{{ route('deliveries.show', $delivery) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                {{ __('Detalhes') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="table-td text-center py-12 text-text-secondary">
                            <i class="fa-solid fa-truck text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                            {{ __('Nenhuma entrega registrada.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($deliveries, 'links'))
        <div class="p-4 border-t border-border dark:border-border-dark">{{ $deliveries->links() }}</div>
        @endif
    </x-card>
</div>

@endsection
