@extends('layouts.app')
@section('content')

<div class="min-h-[80vh] flex items-center justify-center py-12">
    <div class="max-w-lg w-full mx-auto px-4 sm:px-6 lg:px-8">
        <x-card padding="6">
            <div class="text-center mb-6">
                <div class="mx-auto w-14 h-14 bg-primary-50 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-store text-2xl text-primary-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-text-primary dark:text-text-dark">{{ __('Configurar Restaurante') }}</h2>
                <p class="text-sm text-text-secondary mt-1">{{ __('Preencha os dados do seu restaurante para começar.') }}</p>
            </div>

            <form method="POST" action="{{ route('restaurant.store') }}" class="space-y-5">
                @csrf

                <x-input label="{{ __('Nome do Restaurante') }}" name="name" type="text" placeholder="{{ __('Ex: Restaurante Sabor') }}" :value="old('name')" required />

                <x-input label="{{ __('E-mail') }}" name="email" type="email" placeholder="{{ __('contato@restaurante.com') }}" :value="old('email')" required />

                <x-input label="{{ __('Telefone') }}" name="phone" type="text" placeholder="{{ __('(11) 99999-9999') }}" :value="old('phone')" required />

                <div>
                    <x-input-label for="address" value="{{ __('Endereço') }}" />
                    <textarea id="address" name="address" rows="3" class="input-field mt-1">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-text-secondary hover:text-text-primary dark:hover:text-text-dark transition-colors">{{ __('Pular por enquanto') }}</a>
                    <x-button variant="primary" size="lg">
                        <i class="fa-solid fa-plus text-sm"></i>
                        {{ __('Salvar Restaurante') }}
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
