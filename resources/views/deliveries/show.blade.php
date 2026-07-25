@extends('layouts.app')
@section('title', __('Entrega') . ' #' . $delivery->id)
@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('deliveries.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i> {{ __('Voltar às Entregas') }}
        </a>
    </div>

    <div class="page-header flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h1 class="page-title">{{ __('Entrega') }} #{{ $delivery->id }}</h1>
            @php
            $statusClasses = ['pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300', 'in_transit' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300', 'delivered' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300', 'failed' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300'];
            $statusLabels = ['pending' => 'Pendente', 'in_transit' => 'Em Trânsito', 'delivered' => 'Entregue', 'failed' => 'Falha'];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClasses[$delivery->status] ?? '' }}">
                {{ $statusLabels[$delivery->status] ?? $delivery->status }}
            </span>
        </div>
        <a href="{{ route('deliveries.edit', $delivery) }}" class="btn-secondary">
            <i class="fa-regular fa-pen-to-square text-sm"></i> {{ __('Editar') }}
        </a>
    </div>

    <x-card padding="6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Pedido') }}</p>
                <p class="text-sm font-medium">#{{ $delivery->order->order_number ?? $delivery->order_id }}</p>
            </div>
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Tipo') }}</p>
                <p class="text-sm font-medium">{{ $delivery->type === 'delivery' ? 'Entrega' : 'Retirada' }}</p>
            </div>
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Cliente') }}</p>
                <p class="text-sm font-medium">{{ $delivery->contact_name ?? $delivery->order?->customer?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Telefone') }}</p>
                <p class="text-sm font-medium">{{ $delivery->contact_phone ?? $delivery->order?->customer?->phone ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Endereço') }}</p>
                <p class="text-sm font-medium">{{ $delivery->address ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Previsão') }}</p>
                <p class="text-sm font-medium">{{ $delivery->estimated_delivery_at ? $delivery->estimated_delivery_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Entregue em') }}</p>
                <p class="text-sm font-medium">{{ $delivery->delivered_at ? $delivery->delivered_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
        </div>

        @if ($delivery->notes)
        <div class="mt-6 pt-4 border-t border-border dark:border-border-dark">
            <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Observações') }}</p>
            <p class="text-sm">{{ $delivery->notes }}</p>
        </div>
        @endif

        @if ($delivery->order)
        <div class="mt-6 pt-4 border-t border-border dark:border-border-dark">
            <h3 class="text-sm font-semibold text-text-primary mb-3">{{ __('Itens do Pedido') }}</h3>
            <div class="space-y-2">
                @foreach ($delivery->order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-text-secondary">{{ $item->quantity }}x {{ $item->dish_name }}</span>
                    <span class="font-medium">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
                </div>
                @endforeach
                <div class="flex justify-between text-sm font-bold pt-2 border-t border-border dark:border-border-dark">
                    <span>{{ __('Total') }}</span>
                    <span>R$ {{ number_format($delivery->order->total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        @endif
    </x-card>
</div>

@endsection
