@extends('layouts.app')
@section('title', $delivery ? __('Editar Entrega') : __('Nova Entrega'))
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ $delivery ? __('Editar Entrega') : __('Nova Entrega') }}</h1>
        <p class="page-subtitle">{{ $delivery ? __('Atualizar informações da entrega') : __('Registrar uma nova entrega') }}</p>
    </div>

    <x-card padding="6">
        <form method="POST" action="{{ $delivery ? route('deliveries.update', $delivery) : route('deliveries.store') }}">
            @csrf
            @if ($delivery) @method('PUT') @endif

            <div class="space-y-6">
                @if (!$delivery)
                <div>
                    <x-input-label for="order_id" value="{{ __('Pedido') }}" />
                    <select id="order_id" name="order_id" class="input-field mt-1.5" required>
                        <option value="">{{ __('Selecione um pedido') }}</option>
                        @foreach ($orders as $order)
                        <option value="{{ $order->id }}" {{ old('order_id', $selectedOrder?->id) == $order->id ? 'selected' : '' }}>
                            #{{ $order->order_number }} - {{ $order->customer?->name ?? 'Avulso' }} - R$ {{ number_format($order->total, 2, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('order_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="type" value="{{ __('Tipo') }}" />
                    <select id="type" name="type" class="input-field mt-1.5" required>
                        <option value="delivery" {{ old('type', $delivery->type ?? '') === 'delivery' ? 'selected' : '' }}>{{ __('Entrega') }}</option>
                        <option value="pickup" {{ old('type', $delivery->type ?? '') === 'pickup' ? 'selected' : '' }}>{{ __('Retirada') }}</option>
                    </select>
                </div>
                @else
                <div class="p-3 rounded-lg bg-surface dark:bg-surface-dark">
                    <p class="text-sm text-text-secondary">{{ __('Pedido') }}: <span class="font-medium text-text-primary">#{{ $delivery->order->order_number ?? $delivery->order_id }}</span></p>
                </div>
                @endif

                <div>
                    <x-input-label for="address" value="{{ __('Endereço') }}" />
                    <textarea id="address" name="address" rows="2" placeholder="Endereço de entrega" class="input-field mt-1.5 min-h-[60px]">{{ old('address', $delivery->address ?? '') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="contact_name" value="{{ __('Nome do Contato') }}" />
                        <x-text-input id="contact_name" name="contact_name" type="text" placeholder="Nome do contato" class="mt-1.5 block w-full input-field" :value="old('contact_name', $delivery->contact_name ?? '')" />
                    </div>
                    <div>
                        <x-input-label for="contact_phone" value="{{ __('Telefone') }}" />
                        <x-text-input id="contact_phone" name="contact_phone" type="text" placeholder="Telefone do contato" class="mt-1.5 block w-full input-field" :value="old('contact_phone', $delivery->contact_phone ?? '')" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="status" value="{{ __('Status') }}" />
                        <select id="status" name="status" class="input-field mt-1.5" required>
                            <option value="pending" {{ old('status', $delivery->status ?? '') === 'pending' ? 'selected' : '' }}>{{ __('Pendente') }}</option>
                            <option value="in_transit" {{ old('status', $delivery->status ?? '') === 'in_transit' ? 'selected' : '' }}>{{ __('Em Trânsito') }}</option>
                            <option value="delivered" {{ old('status', $delivery->status ?? '') === 'delivered' ? 'selected' : '' }}>{{ __('Entregue') }}</option>
                            <option value="failed" {{ old('status', $delivery->status ?? '') === 'failed' ? 'selected' : '' }}>{{ __('Falha') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="estimated_delivery_at" value="{{ __('Previsão de Entrega') }}" />
                        <x-text-input id="estimated_delivery_at" name="estimated_delivery_at" type="datetime-local" placeholder="Data prevista" class="mt-1.5 block w-full input-field" :value="old('estimated_delivery_at', $delivery?->estimated_delivery_at?->format('Y-m-d\TH:i'))" />
                    </div>
                </div>

                @if ($delivery)
                <div>
                    <x-input-label for="delivered_at" value="{{ __('Entregue em') }}" />
                    <x-text-input id="delivered_at" name="delivered_at" type="datetime-local" placeholder="Data da entrega" class="mt-1.5 block w-full input-field" :value="old('delivered_at', $delivery?->delivered_at?->format('Y-m-d\TH:i'))" />
                </div>
                @endif

                <div>
                    <x-input-label for="notes" value="{{ __('Observações') }}" />
                    <textarea id="notes" name="notes" rows="3" placeholder="Observações da entrega" class="input-field mt-1.5 min-h-[80px]">{{ old('notes', $delivery->notes ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8">
                <x-button variant="primary" size="lg">{{ $delivery ? __('Atualizar') : __('Criar Entrega') }}</x-button>
                <a href="{{ route('deliveries.index') }}" class="btn-secondary">{{ __('Cancelar') }}</a>
            </div>
        </form>
    </x-card>
</div>

@endsection
