@extends('layouts.app')
@section('title', __('Editar Pedido #') . $order->id)
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i> {{ __('Voltar ao Pedido') }}
        </a>
    </div>

    <div class="page-header">
        <h1 class="page-title">{{ __('Editar Pedido #') . $order->order_number }}</h1>
        <p class="page-subtitle">{{ __('Atualizar itens, cliente ou valores do pedido') }}</p>
    </div>

    <form method="POST" action="{{ route('orders.update', $order) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card padding="5">
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Dados do Cliente') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="customer_id" value="{{ __('Cliente') }}" />
                            <select id="customer_id" name="customer_id" class="input-field mt-1.5">
                                <option value="">{{ __('Cliente Avulso') }}</option>
                                @foreach (\App\Models\Customer::where('restaurant_id', $order->restaurant_id)->get() as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->name }} - {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="payment_method" value="{{ __('Pagamento') }}" />
                            <select id="payment_method" name="payment_method" class="input-field mt-1.5">
                                <option value="pix" {{ old('payment_method', $order->payment_method) === 'pix' ? 'selected' : '' }}>PIX</option>
                                <option value="cash" {{ old('payment_method', $order->payment_method) === 'cash' ? 'selected' : '' }}>Dinheiro</option>
                                <option value="credit_card" {{ old('payment_method', $order->payment_method) === 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                                <option value="debit_card" {{ old('payment_method', $order->payment_method) === 'debit_card' ? 'selected' : '' }}>Cartão de Débito</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="delivery_type" value="{{ __('Tipo de Entrega') }}" />
                            <select id="delivery_type" name="delivery_type" class="input-field mt-1.5">
                                <option value="delivery" {{ old('delivery_type', $order->delivery_type) === 'delivery' ? 'selected' : '' }}>{{ __('Entrega') }}</option>
                                <option value="pickup" {{ old('delivery_type', $order->delivery_type) === 'pickup' ? 'selected' : '' }}>{{ __('Retirada') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-input-label for="delivery_address" value="{{ __('Endereço de Entrega') }}" />
                        <textarea id="delivery_address" name="delivery_address" rows="2" placeholder="Endereço de entrega" class="input-field mt-1.5 min-h-[60px]">{{ old('delivery_address', $order->delivery_address) }}</textarea>
                    </div>
                    <div class="mt-4">
                        <x-input-label for="notes" value="{{ __('Observações') }}" />
                        <textarea id="notes" name="notes" rows="2" placeholder="Observações do pedido" class="input-field mt-1.5 min-h-[60px]">{{ old('notes', $order->notes) }}</textarea>
                    </div>
                </x-card>

                <x-card padding="5">
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Itens do Pedido') }}</h3>
                    <div class="space-y-3">
                        @foreach ($order->items as $item)
                        <div class="flex items-center gap-4 p-3 rounded-xl border border-border dark:border-border-dark bg-surface dark:bg-surface-dark">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-text-primary">{{ $item->dish_name }}</p>
                                <p class="text-xs text-text-secondary">
                                    @if ($item->size)
                                    {{ ['small' => 'P', 'medium' => 'M', 'large' => 'G'][$item->size] ?? $item->size }} ·
                                    @endif
                                    R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                <input type="number" name="items[{{ $item->id }}][quantity]" value="{{ $item->quantity }}" min="0" class="input-field w-16 text-sm text-center">
                                <input type="hidden" name="items[{{ $item->id }}][dish_name]" value="{{ $item->dish_name }}">
                                <input type="hidden" name="items[{{ $item->id }}][unit_price]" value="{{ $item->unit_price }}">
                                <input type="hidden" name="items[{{ $item->id }}][size]" value="{{ $item->size }}">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card padding="5" class="sticky top-6">
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Resumo Financeiro') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <x-input-label for="subtotal" value="{{ __('Subtotal (R$)') }}" />
                            <x-text-input id="subtotal" name="subtotal" type="number" step="0.01" placeholder="Valor do subtotal" class="mt-1 block w-full input-field" :value="old('subtotal', $order->subtotal)" />
                        </div>
                        <div>
                            <x-input-label for="delivery_fee" value="{{ __('Taxa de Entrega (R$)') }}" />
                            <x-text-input id="delivery_fee" name="delivery_fee" type="number" step="0.01" placeholder="Valor da taxa" class="mt-1 block w-full input-field" :value="old('delivery_fee', $order->delivery_fee)" />
                        </div>
                        <div>
                            <x-input-label for="discount" value="{{ __('Desconto (R$)') }}" />
                            <x-text-input id="discount" name="discount" type="number" step="0.01" placeholder="Valor do desconto" class="mt-1 block w-full input-field" :value="old('discount', $order->discount)" />
                        </div>
                        <div>
                            <x-input-label for="total" value="{{ __('Total (R$)') }}" />
                            <x-text-input id="total" name="total" type="number" step="0.01" placeholder="Valor total" class="mt-1 block w-full input-field" :value="old('total', $order->total)" />
                        </div>
                    </div>
                    <div class="mt-6">
                        <x-button variant="primary" size="lg" class="w-full">
                            <i class="fa-regular fa-circle-check text-sm"></i>
                            {{ __('Salvar Alterações') }}
                        </x-button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</div>

@endsection
