@extends('layouts.app')
@section('content')

<div class="max-w-3xl mx-auto">
    <div class="page-header flex items-center gap-3">
        <div class="section-icon">
            <i class="fa-solid fa-gear text-xl"></i>
        </div>
        <div>
            <h1 class="page-title">{{ __('Configurações do Restaurante') }}</h1>
            <p class="page-subtitle">{{ __('Atualize as informações do seu restaurante') }}</p>
        </div>
    </div>

    <x-card padding="6">
        <form method="POST" action="{{ route('restaurant.update', $restaurant) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input label="{{ __('Nome do Restaurante') }}" name="name" type="text" placeholder="{{ __('Ex: Restaurante Sabor') }}" :value="old('name', $restaurant->name)" required />

                <x-input label="{{ __('E-mail') }}" name="email" type="email" placeholder="{{ __('contato@restaurante.com') }}" :value="old('email', $restaurant->email)" required />

                <x-input label="{{ __('Telefone') }}" name="phone" type="text" placeholder="{{ __('(11) 99999-9999') }}" :value="old('phone', $restaurant->phone)" required />

                <x-input label="{{ __('WhatsApp') }}" name="whatsapp" type="text" placeholder="{{ __('(11) 99999-9999') }}" :value="old('whatsapp', $restaurant->whatsapp ?? '')" />

                <div class="md:col-span-2">
                    <x-input-label for="address" value="{{ __('Endereço') }}" />
                    <textarea id="address" name="address" rows="3" class="input-field mt-1">{{ old('address', $restaurant->address) }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <x-input label="{{ __('Taxa de Entrega (R$)') }}" name="delivery_fee" type="number" step="0.01" :value="old('delivery_fee', $restaurant->delivery_fee ?? '')" />

                <x-input label="{{ __('Chave PIX') }}" name="pix_key" type="text" placeholder="{{ __('CPF, CNPJ, e-mail ou telefone') }}" :value="old('pix_key', $restaurant->pix_key ?? '')" />
            </div>

            <div class="flex items-center justify-end gap-4 pt-6 divider">
                <a href="{{ route('dashboard') }}" class="btn-ghost">{{ __('Cancelar') }}</a>
                <x-button variant="primary" size="lg">
                    <i class="fa-regular fa-circle-check text-sm"></i>
                    {{ __('Salvar Alterações') }}
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
