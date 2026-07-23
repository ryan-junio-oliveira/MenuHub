@extends('layouts.app')
@section('title', __('Pedidos'))
@section('content')

<div>
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ __('Pedidos') }}</h1>
            <p class="page-subtitle">{{ __('Gerenciar todos os pedidos recebidos') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.kanban') }}">
                <x-button variant="secondary" size="sm">
                    <i class="fa-solid fa-columns text-sm"></i>
                    {{ __('Kanban') }}
                </x-button>
            </a>
            <a href="{{ route('orders.create') }}">
                <x-button variant="primary" size="sm">
                    <i class="fa-solid fa-plus text-sm"></i>
                    {{ __('Novo Pedido') }}
                </x-button>
            </a>
        </div>
    </div>

    <x-card padding="0">
        <div class="p-4 border-b border-border dark:border-border-dark bg-surface dark:bg-surface-dark">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-sm"></i>
                    <input type="text" placeholder="{{ __('Buscar pedidos...') }}" class="input-field pl-9 w-56">
                </div>
                <select class="input-field w-40">
                    <option value="">{{ __('Todos os Status') }}</option>
                    <option value="received">{{ __('Recebido') }}</option>
                    <option value="preparing">{{ __('Em Preparo') }}</option>
                    <option value="out_for_delivery">{{ __('Saiu para Entrega') }}</option>
                    <option value="completed">{{ __('Finalizado') }}</option>
                    <option value="canceled">{{ __('Cancelado') }}</option>
                </select>
                <input type="date" class="input-field w-36">
                <span class="text-xs text-text-secondary">{{ __('até') }}</span>
                <input type="date" class="input-field w-36">
            </div>
        </div>

        <x-table :headers="[__('Pedido #'), __('Cliente'), __('Itens'), __('Total'), __('Status'), __('Data')]" actions>
            @forelse ($orders ?? [] as $order)
            <tr class="table-row">
                <td class="table-td">
                    <span class="font-medium text-text-primary dark:text-text-dark">#{{ $order->id }}</span>
                </td>
                <td class="table-td">
                    <span class="text-text-secondary">{{ $order->customer->name ?? __('Cliente Avulso') }}</span>
                </td>
                <td class="table-td">
                    <span class="text-text-secondary">{{ $order->items_count ?? count($order->items) }}</span>
                </td>
                <td class="table-td">
                    <span class="font-medium text-text-primary dark:text-text-dark">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                </td>
                <td class="table-td">
                    <x-status-badge :status="$order->status" />
                </td>
                <td class="table-td">
                    <span class="text-sm text-text-secondary">{{ $order->created_at instanceof \Carbon\Carbon ? $order->created_at->format('d/m H:i') : $order->created_at }}</span>
                </td>
                <td class="table-td text-right">
                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                        {{ __('Visualizar') }}
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-16">
                    <x-empty-state
                        title="{{ __('Nenhum pedido encontrado') }}"
                        description="{{ __('Crie seu primeiro pedido para começar.') }}"
                        action="{{ __('Novo Pedido') }}"
                        actionUrl="{{ route('orders.create') }}"
                    />
                </td>
            </tr>
            @endforelse
        </x-table>

        @if (method_exists($orders ?? [], 'links'))
        <div class="p-4 border-t border-border dark:border-border-dark">
            {{ $orders->links() }}
        </div>
        @endif
    </x-card>
</div>

@endsection
