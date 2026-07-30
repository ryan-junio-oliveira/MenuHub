@extends('layouts.app')
@section('title', __('Categorias'))
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ __('Categorias de Pratos') }}</h1>
            <p class="page-subtitle">{{ __('Gerenciar suas categorias de cardápio') }}</p>
        </div>
        <x-button variant="primary" size="md" :href="route('dish-categories.create')">
            <i class="fa-solid fa-plus text-sm"></i>
            {{ __('Nova Categoria') }}
        </x-button>
    </div>

    <x-card padding="0">
        <div class="overflow-x-auto">
            <x-table :headers="[__('Nome'), __('Descrição'), __('Status'), __('Pratos')]" actions>
                @forelse ($categories ?? [] as $category)
                <tr class="table-row">
                    <td class="table-td font-medium text-text-primary dark:text-text-dark">{{ $category->name }}</td>
                    <td class="table-td text-text-secondary">{{ $category->description ?? '-' }}</td>
                    <td class="table-td">
                        <x-status-badge :status="$category->is_active ? 'active' : 'inactive'" />
                    </td>
                    <td class="table-td text-text-secondary">{{ $category->dishes_count ?? 0 }}</td>
                    <td class="table-td text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('dish-categories.show', $category) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-primary-600 hover:bg-primary-50 transition-colors" title="Visualizar">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('dish-categories.edit', $category) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-text-primary dark:hover:text-text-dark hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Editar">
                                <i class="fa-regular fa-pen-to-square text-sm"></i>
                            </a>
                            <button type="button" x-on:click="if(confirm('{{ __("Excluir esta categoria?") }}')) document.getElementById('delete-category-{{ $category->id }}').submit()" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Excluir">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                            <form id="delete-category-{{ $category->id }}" method="POST" action="{{ route('dish-categories.destroy', $category) }}" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12">
                        <x-empty-state
                            title="{{ __('Nenhuma categoria ainda') }}"
                            description="{{ __('Comece criando sua primeira categoria.') }}"
                            action="{{ __('Nova Categoria') }}"
                            actionUrl="{{ route('dish-categories.create') }}"
                        />
                    </td>
                </tr>
                @endforelse
            </x-table>
        </div>
    </x-card>
</div>

@endsection
