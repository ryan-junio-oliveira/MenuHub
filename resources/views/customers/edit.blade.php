@extends('layouts.app')
@section('title', __('Editar Cliente'))
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Editar Cliente') }}</h1>
        <p class="page-subtitle">{{ __('Atualizar dados do cliente') }}</p>
    </div>

    <x-card padding="6">
        <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-input
                label="{{ __('Nome Completo') }}"
                name="name"
                type="text"
                placeholder="{{ __('Nome do cliente') }}"
                :error="$errors->first('name')"
                value="{{ old('name', $customer->name) }}"
                required
            />

            <x-input
                label="{{ __('Telefone') }}"
                name="phone"
                type="text"
                placeholder="{{ __('(11) 99999-9999') }}"
                :error="$errors->first('phone')"
                value="{{ old('phone', $customer->phone) }}"
                required
            />

            <x-input
                label="{{ __('E-mail') }}"
                name="email"
                type="email"
                placeholder="{{ __('cliente@exemplo.com') }}"
                :error="$errors->first('email')"
                value="{{ old('email', $customer->email) }}"
            />

            <div>
                <x-input-label for="address" value="{{ __('Endereço') }}" />
                <textarea id="address" name="address" rows="3" class="input-field min-h-[80px] mt-1" placeholder="{{ __('Endereço de entrega') }}">{{ old('address', $customer->address) }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="notes" value="{{ __('Observações') }}" />
                <textarea id="notes" name="notes" rows="2" class="input-field min-h-[60px] mt-1" placeholder="{{ __('Observações sobre o cliente') }}">{{ old('notes', $customer->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button variant="primary" size="md" type="submit">{{ __('Atualizar Cliente') }}</x-button>
                <x-button variant="ghost" size="md" :href="route('customers.index')">{{ __('Cancelar') }}</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
