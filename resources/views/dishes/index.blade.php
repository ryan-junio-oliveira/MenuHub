@extends('layouts.app')
@section('title', __('Pratos'))
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ __('Pratos') }}</h1>
            <p class="page-subtitle">{{ __('Gerenciar seus itens do cardápio') }}</p>
        </div>
        <x-button variant="primary" size="md" :href="route('dishes.create')">
            <i class="fa-solid fa-plus text-sm"></i>
            {{ __('Novo Prato') }}
        </x-button>
    </div>

    <x-card padding="0">
        <div class="p-4 border-b border-border dark:border-border-dark bg-surface dark:bg-surface-dark">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-text-secondary"></i>
                        <input type="text" placeholder="{{ __('Buscar pratos...') }}" class="input-field pl-9">
                    </div>
                </div>
                <select class="input-field w-44">
                    <option value="">{{ __('Todas as Categorias') }}</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <x-table :headers="[__('Prato'), __('Categoria'), __('Preço'), __('Status')]" actions>
                @forelse ($dishes ?? [] as $dish)
                <tr class="table-row">
                    <td class="table-td">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-500 shrink-0">
                                <i class="fa-solid fa-utensils text-base"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-text-primary dark:text-text-dark">{{ $dish->name }}</p>
                                <p class="text-xs text-text-secondary">{{ $dish->description ? Str::limit($dish->description, 40) : '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="table-td text-text-secondary">{{ $dish->category->name ?? '-' }}</td>
                    <td class="table-td font-medium text-text-primary dark:text-text-dark">R$ {{ number_format($dish->price, 2, ',', '.') }}</td>
                    <td class="table-td">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $dish->is_available ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dish->is_available ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $dish->is_available ? __('Disponível') : __('Indisponível') }}
                        </span>
                    </td>
                    <td class="table-td text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('dishes.edit', $dish) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-text-primary dark:hover:text-text-dark hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Editar">
                                <i class="fa-regular fa-pen-to-square text-sm"></i>
                            </a>
                            <button type="button" x-on:click="if(confirm('{{ __("Excluir este prato?") }}')) document.getElementById('delete-dish-{{ $dish->id }}').submit()" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Excluir">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                            <form id="delete-dish-{{ $dish->id }}" method="POST" action="{{ route('dishes.destroy', $dish) }}" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12">
                        <x-empty-state
                            title="{{ __('Nenhum prato ainda') }}"
                            description="{{ __('Comece criando seu primeiro prato.') }}"
                            action="{{ __('Novo Prato') }}"
                            actionUrl="{{ route('dishes.create') }}"
                        />
                    </td>
                </tr>
                @endforelse
            </x-table>
        </div>
    </x-card>
</div>

@endsection
