@extends('layouts.app')
@section('title', __('Pedido #') . $order->id)
@section('content')

<div>
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary dark:hover:text-text-dark transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
            {{ __('Voltar aos Pedidos') }}
        </a>
    </div>

    <div class="page-header flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <h1 class="page-title">{{ __('Pedido') }} #{{ $order->id }}</h1>
            <x-status-badge :status="$order->status" />
        </div>
        <p class="text-sm text-text-secondary">{{ $order->created_at instanceof \Carbon\Carbon ? $order->created_at->format('d/m/Y \à\s H:i') : $order->created_at }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <x-card padding="5" class="lg:col-span-2">
            <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark uppercase tracking-wider mb-4">{{ __('Itens do Pedido') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border dark:divide-border-dark">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('Prato') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('Tamanho') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('Qtd') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('Preço') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border dark:divide-border-dark">
                        @foreach ($order->items as $item)
                        <tr class="hover:bg-surface dark:hover:bg-surface-dark/50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-text-primary dark:text-text-dark">{{ $item->dish->name ?? $item->dish_name }}</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">{{ ucfirst($item->size) }}</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-text-primary dark:text-text-dark">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <div class="space-y-6">
            <x-card padding="5">
                <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark uppercase tracking-wider mb-3">{{ __('Cliente') }}</h3>
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-regular fa-user text-lg text-primary-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-text-primary dark:text-text-dark">{{ $order->customer->name ?? __('Cliente Avulso') }}</p>
                        @if ($order->customer)
                            <p class="text-xs text-text-secondary mt-0.5">{{ $order->customer->phone }}</p>
                        @endif
                    </div>
                </div>
            </x-card>

            <x-card padding="5">
                <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark uppercase tracking-wider mb-3">{{ __('Resumo do Pedido') }}</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">{{ __('Subtotal') }}</span>
                        <span class="text-text-primary dark:text-text-dark">R$ {{ number_format($order->subtotal ?? $order->total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">{{ __('Taxa de Entrega') }}</span>
                        <span class="text-text-primary dark:text-text-dark">R$ {{ number_format($order->delivery_fee ?? 0, 2, ',', '.') }}</span>
                    </div>
                    @if ($order->discount ?? false)
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">{{ __('Desconto') }}</span>
                        <span class="text-green-600 dark:text-green-400">-R$ {{ number_format($order->discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm font-semibold pt-2 border-t border-border dark:border-border-dark">
                        <span class="text-text-primary dark:text-text-dark">{{ __('Total') }}</span>
                        <span class="text-text-primary dark:text-text-dark">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    @if ($order->notes)
    <x-card padding="5" class="mb-6">
        <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark uppercase tracking-wider mb-2">{{ __('Observações') }}</h3>
        <p class="text-sm text-text-secondary">{{ $order->notes }}</p>
    </x-card>
    @endif

    <x-card padding="5">
        <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark uppercase tracking-wider mb-4">{{ __('Atualizar Status') }}</h3>
        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="received">
                <x-button variant="secondary" size="sm" :disabled="$order->status === 'received'">
                    <i class="fa-regular fa-circle-check text-sm"></i>
                    {{ __('Recebido') }}
                </x-button>
            </form>
            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="preparing">
                <x-button variant="primary" size="sm" :disabled="$order->status === 'preparing'">
                    <i class="fa-solid fa-fire text-sm"></i>
                    {{ __('Em Preparo') }}
                </x-button>
            </form>
            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="out_for_delivery">
                <x-button variant="secondary" size="sm" :disabled="$order->status === 'out_for_delivery'">
                    <i class="fa-solid fa-truck text-sm"></i>
                    {{ __('Saiu para Entrega') }}
                </x-button>
            </form>
            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="completed">
                <x-button variant="success" size="sm" :disabled="$order->status === 'completed'">
                    <i class="fa-regular fa-circle-check text-sm"></i>
                    {{ __('Finalizar') }}
                </x-button>
            </form>
            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="canceled">
                <x-button variant="danger" size="sm" :disabled="$order->status === 'canceled'" x-on:click="if(!confirm('{{ __('Cancelar este pedido?') }}')) $event.preventDefault()">
                    <i class="fa-solid fa-xmark text-sm"></i>
                    {{ __('Cancelar Pedido') }}
                </x-button>
            </form>
        </div>
    </x-card>
</div>

@endsection
