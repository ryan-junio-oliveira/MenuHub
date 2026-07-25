@extends('layouts.app')
@section('title', __('Clientes'))
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ __('Clientes') }}</h1>
            <p class="page-subtitle">{{ __('Visualizar e gerenciar seus clientes') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('customer-tags.index') }}" class="btn-secondary">
                <i class="fa-solid fa-tags text-sm"></i>
                {{ __('Gerenciar Tags') }}
            </a>
            <x-button variant="primary" size="md" :href="route('customers.create')">
                <i class="fa-solid fa-plus text-sm"></i>
                {{ __('Novo Cliente') }}
            </x-button>
        </div>
    </div>

    <x-card padding="0">
        <div class="p-4 border-b border-border dark:border-border-dark bg-surface dark:bg-surface-dark">
            <form method="GET" class="flex items-center gap-3 flex-wrap">
                <div class="relative flex-1 max-w-md">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-text-secondary"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Buscar cliente...') }}" class="input-field pl-9">
                </div>
                <select name="tag_id" class="input-field w-44" onchange="this.form.submit()">
                    <option value="">{{ __('Todas as tags') }}</option>
                    @foreach ($tags ?? [] as $tag)
                    <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                    @endforeach
                </select>
                @if (request()->anyFilled(['search', 'tag_id']))
                <a href="{{ route('customers.index') }}" class="text-sm text-text-secondary hover:text-text-primary">{{ __('Limpar') }}</a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <x-table :headers="[__('Nome'), __('Telefone'), __('Email'), __('Tags'), __('Pedidos'), __('Total Gasto')]" actions>
                @forelse ($customers ?? [] as $customer)
                <tr class="table-row">
                    <td class="table-td">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                <span class="text-sm font-medium text-primary-600 dark:text-primary-400">{{ substr($customer->name, 0, 2) }}</span>
                            </div>
                            <a href="{{ route('customers.show', $customer) }}" class="text-sm font-medium text-text-primary dark:text-text-dark hover:text-primary-600 dark:hover:text-primary-400">{{ $customer->name }}</a>
                        </div>
                    </td>
                    <td class="table-td text-text-secondary">{{ $customer->phone ?? '-' }}</td>
                    <td class="table-td text-text-secondary">{{ $customer->email ?? '-' }}</td>
                    <td class="table-td">
                        <div class="flex items-center gap-1 flex-wrap">
                            @forelse ($customer->tags as $tag)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                            @empty
                            <span class="text-xs text-text-secondary">-</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="table-td text-text-secondary">{{ $customer->orders_count ?? 0 }}</td>
                    <td class="table-td text-text-secondary">R$ {{ number_format($customer->total_spent ?? 0, 2, ',', '.') }}</td>
                    <td class="table-td text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" title="Visualizar">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-text-primary dark:hover:text-text-dark hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Editar">
                                <i class="fa-regular fa-pen-to-square text-sm"></i>
                            </a>
                            <button type="button" x-on:click="if(confirm('{{ __("Excluir este cliente?") }}')) document.getElementById('delete-customer-{{ $customer->id }}').submit()" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Excluir">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                            <form id="delete-customer-{{ $customer->id }}" method="POST" action="{{ route('customers.destroy', $customer) }}" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12">
                        <x-empty-state
                            title="{{ __('Nenhum cliente ainda') }}"
                            description="{{ __('Clientes aparecerão aqui após realizarem pedidos.') }}"
                        />
                    </td>
                </tr>
                @endforelse
            </x-table>
        </div>
    </x-card>
    @if (method_exists($customers, 'links'))
    <div class="mt-4">{{ $customers->links() }}</div>
    @endif
</div>

@endsection
