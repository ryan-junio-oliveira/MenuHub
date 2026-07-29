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
        <form method="POST" action="{{ route('restaurant.update', $restaurant) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input label="{{ __('Nome do Restaurante') }}" name="name" type="text" placeholder="{{ __('Ex: Restaurante Sabor') }}" :value="old('name', $restaurant->name)" required />

                    <x-input label="{{ __('E-mail') }}" name="email" type="email" placeholder="{{ __('contato@restaurante.com') }}" :value="old('email', $restaurant->email)" required />

                    <x-input label="{{ __('Telefone') }}" name="phone" type="text" placeholder="{{ __('(11) 99999-9999') }}" :value="old('phone', $restaurant->phone)" required />

                    <x-input label="{{ __('WhatsApp') }}" name="whatsapp_number" type="text" placeholder="{{ __('(11) 99999-9999') }}" :value="old('whatsapp_number', $restaurant->whatsapp_number ?? '')" />

                    <div class="md:col-span-2">
                        <x-input-label for="address" value="{{ __('Endereço') }}" />
                        <textarea id="address" name="address" rows="3" placeholder="Endereço do restaurante" class="input-field mt-1">{{ old('address', $restaurant->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <x-input label="{{ __('Taxa de Entrega (R$)') }}" name="delivery_fee" type="number" step="0.01" :value="old('delivery_fee', $restaurant->delivery_fee ?? '')" />

                    <x-input label="{{ __('Chave PIX') }}" name="pix_key" type="text" placeholder="{{ __('CPF, CNPJ, e-mail ou telefone') }}" :value="old('pix_key', $restaurant->pix_key ?? '')" />
                </div>

                <div class="border-t border-border dark:border-border-dark pt-6">
                    <p class="text-sm font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Imagens do Restaurante') }}</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="logo" value="{{ __('Logo') }}" />
                            <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg,image/webp" class="input-field mt-1" />
                            @if ($restaurant->logo)
                            <div class="mt-2 flex items-center gap-3">
                                <img src="{{ Storage::url($restaurant->logo) }}" alt="Logo" class="w-12 h-12 rounded-lg object-cover">
                                <span class="text-xs text-text-secondary">{{ __('Logo atual') }}</span>
                            </div>
                            @endif
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="cover" value="{{ __('Capa') }}" />
                            <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/jpg,image/webp" class="input-field mt-1" />
                            @if ($restaurant->cover)
                            <div class="mt-2">
                                <img src="{{ Storage::url($restaurant->cover) }}" alt="Capa" class="w-full h-20 rounded-lg object-cover">
                                <span class="text-xs text-text-secondary mt-1 block">{{ __('Capa atual') }}</span>
                            </div>
                            @endif
                            <x-input-error :messages="$errors->get('cover')" class="mt-2" />
                        </div>
                    </div>
                </div>

                @if ($restaurant->whatsapp_phone_id || $restaurant->whatsapp_api_token || app()->environment('local'))
                <div class="border-t border-border dark:border-border-dark pt-6">
                    <p class="text-sm font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('WhatsApp API') }}</p>
                    @if (app()->environment('local'))
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-4">
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            {{ __('Modo de desenvolvimento: usando MockWhatsAppService. As mensagens serão logadas no console/laravel.log em vez de enviadas via API real.') }}
                        </p>
                    </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="whatsapp_phone_id" value="{{ __('Phone ID') }}" />
                            <x-text-input id="whatsapp_phone_id" name="whatsapp_phone_id" type="text" placeholder="ID do telefone empresarial" class="mt-1 block w-full input-field" :value="old('whatsapp_phone_id', $restaurant->whatsapp_phone_id)" />
                        </div>
                        <div>
                            <x-input-label for="whatsapp_api_token" value="{{ __('API Token') }}" />
                            <x-text-input id="whatsapp_api_token" name="whatsapp_api_token" type="password" placeholder="Token de acesso da API" class="mt-1 block w-full input-field" :value="old('whatsapp_api_token', $restaurant->whatsapp_api_token ? '********' : '')" />
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-border dark:border-border-dark mt-6">
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
