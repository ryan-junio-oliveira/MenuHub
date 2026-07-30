@extends('layouts.app')
@section('title', $dishCategory->name)
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('dish-categories.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i> {{ __('Voltar às Categorias') }}
        </a>
    </div>

    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ $dishCategory->name }}</h1>
            <p class="page-subtitle">{{ __('Categoria de pratos') }}</p>
        </div>
        <a href="{{ route('dish-categories.edit', $dishCategory) }}" class="btn-secondary text-sm">
            <i class="fa-regular fa-pen-to-square text-sm mr-1"></i> {{ __('Editar') }}
        </a>
    </div>

    @if ($dishCategory->description)
    <div class="mb-6 p-4 rounded-xl bg-surface dark:bg-surface-dark border border-border dark:border-border-dark">
        <p class="text-sm text-text-secondary">{{ $dishCategory->description }}</p>
    </div>
    @endif

    <x-card padding="0">
        <div class="p-4 border-b border-border dark:border-border-dark bg-surface dark:bg-surface-dark">
            <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark">{{ __('Pratos nesta Categoria') }} ({{ $dishes->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">{{ __('Prato') }}</th>
                        <th class="table-th">{{ __('Preços') }}</th>
                        <th class="table-th">{{ __('Status') }}</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @forelse ($dishes as $dish)
                    <tr class="table-row">
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                @if ($dish->image)
                                <img src="{{ Storage::url($dish->image) }}" alt="" class="w-9 h-9 rounded-lg object-cover shrink-0">
                                @else
                                <div class="w-9 h-9 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400">{{ substr($dish->name, 0, 2) }}</span>
                                </div>
                                @endif
                                <a href="{{ route('dishes.show', $dish) }}" class="text-sm font-medium text-text-primary dark:text-text-dark hover:text-primary-600">{{ $dish->name }}</a>
                            </div>
                        </td>
                        <td class="table-td text-sm text-text-secondary">
                            @if ($dish->price_small) <span class="mr-2">P: R$ {{ number_format($dish->price_small, 2, ',', '.') }}</span> @endif
                            @if ($dish->price_medium) <span class="mr-2">M: R$ {{ number_format($dish->price_medium, 2, ',', '.') }}</span> @endif
                            @if ($dish->price_large) <span>G: R$ {{ number_format($dish->price_large, 2, ',', '.') }}</span> @endif
                            @if (!$dish->price_small && !$dish->price_medium && !$dish->price_large) - @endif
                        </td>
                        <td class="table-td">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $dish->is_available ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                                {{ $dish->is_available ? __('Disponível') : __('Indisponível') }}
                            </span>
                        </td>
                        <td class="table-td text-right">
                            <a href="{{ route('dishes.show', $dish) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-primary-600 transition-colors" title="Visualizar">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="table-td text-center py-12 text-text-secondary">
                            <i class="fa-solid fa-utensils text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                            {{ __('Nenhum prato nesta categoria.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

@endsection
