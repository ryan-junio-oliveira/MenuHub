@extends('layouts.app')
@section('title', $dish->name)
@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('dishes.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i> {{ __('Voltar aos Pratos') }}
        </a>
    </div>

    <div class="page-header flex items-center justify-between">
        <div class="flex items-center gap-4">
            @if ($dish->image)
            <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0">
                <img src="{{ Storage::url($dish->image) }}" alt="{{ $dish->name }}" class="w-full h-full object-cover">
            </div>
            @else
            <div class="w-16 h-16 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                <span class="text-xl font-bold text-primary-600 dark:text-primary-400">{{ substr($dish->name, 0, 2) }}</span>
            </div>
            @endif
            <div>
                <h1 class="page-title">{{ $dish->name }}</h1>
                <p class="page-subtitle">{{ $dish->category?->name ?? __('Sem categoria') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('dishes.edit', $dish) }}" class="btn-secondary text-sm">
                <i class="fa-regular fa-pen-to-square text-sm mr-1"></i> {{ __('Editar') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-card padding="5">
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Informações') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Nome') }}</p>
                        <p class="text-sm font-medium">{{ $dish->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Categoria') }}</p>
                        <p class="text-sm font-medium">{{ $dish->category?->name ?? '-' }}</p>
                    </div>
                    @if ($dish->description)
                    <div class="md:col-span-2">
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Descrição') }}</p>
                        <p class="text-sm font-medium">{{ $dish->description }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Preço Pequeno') }}</p>
                        <p class="text-sm font-medium">{{ $dish->price_small ? 'R$ '.number_format($dish->price_small, 2, ',', '.') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Preço Médio') }}</p>
                        <p class="text-sm font-medium">{{ $dish->price_medium ? 'R$ '.number_format($dish->price_medium, 2, ',', '.') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Preço Grande') }}</p>
                        <p class="text-sm font-medium">{{ $dish->price_large ? 'R$ '.number_format($dish->price_large, 2, ',', '.') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Limite por Pedido') }}</p>
                        <p class="text-sm font-medium">{{ $dish->max_selections ?? 1 }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        <div>
            <x-card padding="5">
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Status') }}</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text-secondary">{{ __('Disponível') }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dish->is_available ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                            {{ $dish->is_available ? __('Sim') : __('Não') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text-secondary">{{ __('Ativo') }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dish->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                            {{ $dish->is_active ? __('Sim') : __('Não') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text-secondary">{{ __('Gourmet') }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dish->is_gourmet ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">
                            {{ $dish->is_gourmet ? __('Sim') : __('Não') }}
                        </span>
                    </div>
                </div>
            </x-card>

            @if ($dish->image)
            <x-card padding="5" class="mt-4">
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-3">{{ __('Imagem') }}</h3>
                <img src="{{ Storage::url($dish->image) }}" alt="{{ $dish->name }}" class="w-full rounded-lg">
            </x-card>
            @endif
        </div>
    </div>
</div>

@endsection
