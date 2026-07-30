@extends('layouts.app')
@section('title', __('Tags de Clientes'))
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ __('Tags de Clientes') }}</h1>
            <p class="page-subtitle">{{ __('Gerenciar tags para segmentação de clientes') }}</p>
        </div>
        <a href="{{ route('customer-tags.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus text-sm"></i>
            {{ __('Nova Tag') }}
        </a>
    </div>

    <x-card padding="0">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">{{ __('Tag') }}</th>
                        <th class="table-th">{{ __('Clientes') }}</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @forelse ($tags as $tag)
                    <tr class="table-row">
                        <td class="table-td">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $tag->color }}"></span>
                                {{ $tag->name }}
                            </span>
                        </td>
                        <td class="table-td text-sm text-text-secondary">{{ $tag->customers_count }}</td>
                        <td class="table-td text-right">
                            <a href="{{ route('customer-tags.show', $tag) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-primary-600 transition-colors" title="Visualizar">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('customer-tags.edit', $tag) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-text-primary transition-colors">
                                <i class="fa-regular fa-pen-to-square text-sm"></i>
                            </a>
                            <button type="button" onclick="if(confirm('Excluir esta tag?')) document.getElementById('delete-tag-{{ $tag->id }}').submit()" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-red-600 transition-colors">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                            <form id="delete-tag-{{ $tag->id }}" method="POST" action="{{ route('customer-tags.destroy', $tag) }}" class="hidden">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="table-td text-center py-12 text-text-secondary">
                            <i class="fa-solid fa-tags text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                            {{ __('Nenhuma tag criada.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

@endsection
