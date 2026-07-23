@extends('layouts.app')
@section('title', __('Configurações'))
@section('content')

<div class="max-w-4xl mx-auto" x-data="{ activeSection: 'restaurant' }">
    <div class="page-header">
        <h1 class="page-title">{{ __('Configurações') }}</h1>
        <p class="page-subtitle">{{ __('Gerencie as configurações do seu restaurante') }}</p>
    </div>

    <div class="flex flex-wrap gap-3 mb-8">
        <button x-on:click="activeSection = 'restaurant'"
            :class="{ 'bg-primary-600 text-white shadow-md shadow-primary-600/20': activeSection === 'restaurant', 'bg-card dark:bg-card-dark text-text-primary dark:text-text-dark border border-border dark:border-border-dark': activeSection !== 'restaurant' }"
            class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all hover:shadow-md">
            <i class="fa-solid fa-store text-sm inline-block -mt-0.5 mr-1.5"></i>
            {{ __('Informações') }}
        </button>
        <button x-on:click="activeSection = 'hours'"
            :class="{ 'bg-primary-600 text-white shadow-md shadow-primary-600/20': activeSection === 'hours', 'bg-card dark:bg-card-dark text-text-primary dark:text-text-dark border border-border dark:border-border-dark': activeSection !== 'hours' }"
            class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all hover:shadow-md">
            <i class="fa-regular fa-clock text-sm inline-block -mt-0.5 mr-1.5"></i>
            {{ __('Horários') }}
        </button>
        <button x-on:click="activeSection = 'payment'"
            :class="{ 'bg-primary-600 text-white shadow-md shadow-primary-600/20': activeSection === 'payment', 'bg-card dark:bg-card-dark text-text-primary dark:text-text-dark border border-border dark:border-border-dark': activeSection !== 'payment' }"
            class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all hover:shadow-md">
            <i class="fa-regular fa-credit-card text-sm inline-block -mt-0.5 mr-1.5"></i>
            {{ __('Pagamento') }}
        </button>
        <button x-on:click="activeSection = 'delivery'"
            :class="{ 'bg-primary-600 text-white shadow-md shadow-primary-600/20': activeSection === 'delivery', 'bg-card dark:bg-card-dark text-text-primary dark:text-text-dark border border-border dark:border-border-dark': activeSection !== 'delivery' }"
            class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all hover:shadow-md">
            <i class="fa-solid fa-truck text-sm inline-block -mt-0.5 mr-1.5"></i>
            {{ __('Entrega') }}
        </button>
    </div>

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')

        <div x-show="activeSection === 'restaurant'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <x-card padding="6">
                <div class="flex items-center gap-2 mb-6">
                    <i class="fa-solid fa-store text-base text-primary-600"></i>
                    <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Informações do Restaurante') }}</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="name" value="{{ __('Nome do Restaurante') }}" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full input-field" :value="old('name', $settings->name ?? '')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="{{ __('E-mail') }}" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full input-field" :value="old('email', $settings->email ?? '')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="phone" value="{{ __('Telefone') }}" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full input-field" :value="old('phone', $settings->phone ?? '')" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="cnpj" value="{{ __('CNPJ') }}" />
                        <x-text-input id="cnpj" name="cnpj" type="text" class="mt-1 block w-full input-field" :value="old('cnpj', $settings->cnpj ?? '')" />
                        <x-input-error :messages="$errors->get('cnpj')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="address" value="{{ __('Endereço') }}" />
                        <textarea id="address" name="address" rows="3" class="mt-1 block w-full input-field min-h-[80px]">{{ old('address', $settings->address ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                </div>
            </x-card>
        </div>

        <div x-show="activeSection === 'hours'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <x-card padding="6">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa-regular fa-clock text-base text-primary-600"></i>
                    <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Horários de Funcionamento') }}</h3>
                </div>
                <p class="text-sm text-text-secondary dark:text-slate-400 mb-6">{{ __('Configure seus horários de funcionamento para cada dia da semana.') }}</p>

                @foreach ([__('Segunda'), __('Terça'), __('Quarta'), __('Quinta'), __('Sexta'), __('Sábado'), __('Domingo')] as $index => $day)
                <div class="flex flex-wrap items-center gap-4 py-4 {{ $loop->first ? '' : 'border-t border-border dark:border-border-dark' }}">
                    <div class="w-28">
                        <span class="text-sm font-medium text-text-primary dark:text-text-dark">{{ $day }}</span>
                    </div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="days[{{ $day }}][open]" value="1" checked class="rounded border-border dark:border-border-dark text-primary-600 shadow-sm focus:ring-primary-500" />
                            <span class="text-sm text-text-secondary">{{ __('Aberto') }}</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="time" name="days[{{ $day }}][open_time]" value="11:00" class="input-field !w-32 text-sm" />
                            <span class="text-sm text-text-secondary">{{ __('às') }}</span>
                        <input type="time" name="days[{{ $day }}][close_time]" value="22:00" class="input-field !w-32 text-sm" />
                    </div>
                </div>
                @endforeach
            </x-card>
        </div>

        <div x-show="activeSection === 'payment'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <x-card padding="6">
                <div class="flex items-center gap-2 mb-6">
                    <i class="fa-regular fa-credit-card text-base text-primary-600"></i>
                    <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Pagamento e Contato') }}</h3>
                </div>

                <div class="space-y-6">
                    <div>
                        <x-input-label for="pix_key" value="{{ __('Chave PIX') }}" />
                        <x-text-input id="pix_key" name="pix_key" type="text" class="mt-1 block w-full input-field" :value="old('pix_key', $settings->pix_key ?? '')" placeholder="{{ __('CPF, CNPJ, e-mail ou telefone') }}" />
                        <p class="mt-1 text-xs text-text-secondary dark:text-slate-400">{{ __('Usada para geração automática de pagamento PIX.') }}</p>
                        <x-input-error :messages="$errors->get('pix_key')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="whatsapp" value="{{ __('WhatsApp') }}" />
                        <x-text-input id="whatsapp" name="whatsapp" type="text" class="mt-1 block w-full input-field" :value="old('whatsapp', $settings->whatsapp ?? '')" placeholder="{{ __('+55 (11) 99999-8888') }}" />
                        <p class="mt-1 text-xs text-text-secondary dark:text-slate-400">{{ __('Clientes usarão este número para pedidos via WhatsApp.') }}</p>
                        <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="instagram" value="{{ __('Instagram') }}" />
                        <x-text-input id="instagram" name="instagram" type="text" class="mt-1 block w-full input-field" :value="old('instagram', $settings->instagram ?? '')" placeholder="{{ __('@seurestaurante') }}" />
                        <x-input-error :messages="$errors->get('instagram')" class="mt-2" />
                    </div>
                </div>
            </x-card>
        </div>

        <div x-show="activeSection === 'delivery'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <x-card padding="6">
                <div class="flex items-center gap-2 mb-6">
                    <i class="fa-solid fa-truck text-base text-primary-600"></i>
                    <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Configurações de Entrega') }}</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="delivery_fee" value="{{ __('Taxa de Entrega (R$)') }}" />
                        <x-text-input id="delivery_fee" name="delivery_fee" type="number" step="0.01" class="mt-1 block w-full input-field" :value="old('delivery_fee', $settings->delivery_fee ?? '')" />
                        <x-input-error :messages="$errors->get('delivery_fee')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="free_delivery_min" value="{{ __('Mín. para Entrega Grátis (R$)') }}" />
                        <x-text-input id="free_delivery_min" name="free_delivery_min" type="number" step="0.01" class="mt-1 block w-full input-field" :value="old('free_delivery_min', $settings->free_delivery_min ?? '')" />
                        <p class="mt-1 text-xs text-text-secondary dark:text-slate-400">{{ __('Pedidos acima deste valor têm entrega grátis.') }}</p>
                        <x-input-error :messages="$errors->get('free_delivery_min')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="delivery_radius" value="{{ __('Raio de Entrega (km)') }}" />
                        <x-text-input id="delivery_radius" name="delivery_radius" type="number" step="0.1" class="mt-1 block w-full input-field" :value="old('delivery_radius', $settings->delivery_radius ?? '')" />
                        <x-input-error :messages="$errors->get('delivery_radius')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="estimated_delivery_time" value="{{ __('Tempo Estimado (min)') }}" />
                        <x-text-input id="estimated_delivery_time" name="estimated_delivery_time" type="number" class="mt-1 block w-full input-field" :value="old('estimated_delivery_time', $settings->estimated_delivery_time ?? '')" />
                        <x-input-error :messages="$errors->get('estimated_delivery_time')" class="mt-2" />
                    </div>
                </div>
            </x-card>
        </div>

        <div class="flex items-center justify-end gap-4 mt-8">
            <x-button variant="primary" size="lg">
                <i class="fa-regular fa-circle-check text-sm"></i>
                {{ __('Salvar Configurações') }}
            </x-button>
        </div>
    </form>
</div>

@endsection
