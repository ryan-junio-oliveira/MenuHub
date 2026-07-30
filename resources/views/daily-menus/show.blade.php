@extends('layouts.app')
@section('title', __('Cardápio') . ' - ' . ($menu->date instanceof \Carbon\Carbon ? $menu->date->format('d/m/Y') : $menu->date))
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ __('Cardápio de') }} {{ $menu->date instanceof \Carbon\Carbon ? $menu->date->format('l, d \d\e F \d\e Y') : $menu->date }}</h1>
            <p class="page-subtitle">{{ __('Máx.') }} {{ $menu->max_selections }} {{ __('seleções por pedido') }}</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <form method="POST" action="{{ route('daily-menus.dispatch', $menu) }}" class="inline">
                @csrf
                <button type="submit" class="btn-primary">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    {{ __('Enviar Agora') }}
                </button>
            </form>
            <a href="{{ route('daily-menus.edit', $menu) }}" class="btn-secondary">
                <i class="fa-regular fa-pen-to-square text-sm"></i>
                {{ __('Editar') }}
            </a>
            <form method="POST" action="{{ route('daily-menus.destroy', $menu) }}" class="inline" x-data>
                @csrf @method('DELETE')
                <button type="submit" x-on:click.prevent="if(confirm('{{ __("Excluir este cardápio?") }}')) $el.closest('form').submit()" class="btn-danger">
                    <i class="fa-solid fa-trash-can text-sm"></i>
                    {{ __('Excluir') }}
                </button>
            </form>
        </div>
    </div>

    <a href="{{ route('daily-menus.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 mb-6">
        <i class="fa-solid fa-arrow-left text-sm"></i>
        {{ __('Voltar aos Cardápios') }}
    </a>

    <x-card padding="6">
        @forelse ($menu->items->groupBy(fn($item) => $item->dish->category->name ?? 'Geral') as $categoryName => $items)
        <div class="mb-6 last:mb-0">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-1 h-6 rounded-full bg-primary-500"></div>
                <h4 class="text-base font-semibold text-text-primary dark:text-text-dark">{{ $categoryName }}</h4>
                <span class="text-xs text-text-secondary dark:text-slate-400">({{ $items->count() }} {{ $items->count() === 1 ? __('item') : __('itens') }})</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($items as $menuItem)
                <div class="flex items-center justify-between p-4 rounded-xl border border-border dark:border-border-dark bg-slate-50/50 dark:bg-slate-800/30 hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors duration-150">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-text-primary dark:text-text-dark">{{ $menuItem->dish->name }}</p>
                        @if ($menuItem->dish->description)
                        <p class="text-xs text-text-secondary dark:text-slate-400 mt-0.5 truncate">{{ $menuItem->dish->description }}</p>
                        @endif
                    </div>
                    <div class="flex gap-3 text-sm flex-shrink-0 ml-4">
                        @if ($menuItem->price_small)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 text-xs font-medium">
                            <span class="text-xs opacity-60">{{ __('P') }}</span> R$ {{ number_format($menuItem->price_small, 2, ',', '.') }}
                        </span>
                        @endif
                        @if ($menuItem->price_medium)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 text-xs font-medium">
                            <span class="text-xs opacity-60">{{ __('M') }}</span> R$ {{ number_format($menuItem->price_medium, 2, ',', '.') }}
                        </span>
                        @endif
                        @if ($menuItem->price_large)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 text-xs font-medium">
                            <span class="text-xs opacity-60">{{ __('G') }}</span> R$ {{ number_format($menuItem->price_large, 2, ',', '.') }}
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
         <div class="text-center py-16">
            <i class="fa-regular fa-inbox text-3xl mx-auto text-slate-300 dark:text-slate-600 mb-4"></i>
            <p class="text-sm text-text-secondary dark:text-slate-400">{{ __('Nenhum prato neste cardápio.') }}</p>
        </div>
        @endforelse

        @php
            $groupedOptions = $menu->options->groupBy(fn($o) => $o->category?->name ?? 'Geral');
        @endphp
        @if ($groupedOptions->isNotEmpty())
        <div class="mt-8 pt-6 border-t border-border dark:border-border-dark">
            <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-5 flex items-center gap-2">
                <i class="fa-solid fa-utensils text-sm text-primary-600"></i>
                {{ __('Opcoes do Marmitex') }}
            </h3>
            @foreach ($groupedOptions as $catName => $opts)
            <div class="mb-5 last:mb-0">
                <h4 class="text-sm font-semibold text-text-secondary dark:text-slate-400 mb-2 uppercase tracking-wide">{{ $catName }}</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach ($opts as $opt)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-sm text-text-primary dark:text-text-dark font-medium">
                        {{ $opt->name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="mt-8 pt-6 border-t border-border dark:border-border-dark flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-text-secondary dark:text-slate-400">
                <i class="fa-solid fa-shield-halved text-sm"></i>
                {{ $menu->items->count() }} {{ __('itens') }} · {{ __('Máx') }} {{ $menu->max_selections }} {{ __('por pedido') }}
            </div>
            <button x-on:click="navigator.clipboard.writeText('{{ route('daily-menus.show', $menu) }}'); $el.classList.add('text-green-600'); setTimeout(() => $el.classList.remove('text-green-600'), 2000)" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors">
                <i class="fa-solid fa-clipboard-list text-sm"></i>
                {{ __('Copiar link compartilhável') }}
            </button>
        </div>
    </x-card>
</div>

@endsection
