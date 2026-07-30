@extends('layouts.app')
@section('title', $option->name)
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('menu-options.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
                <i class="fa-solid fa-arrow-left text-sm"></i> {{ __('Voltar as Opcoes') }}
            </a>
        </div>
        <h1 class="page-title">{{ $option->name }}</h1>
        <p class="page-subtitle">{{ $option->category?->name ?? __('Sem categoria') }}</p>
    </div>

    <x-card padding="6">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-text-secondary">{{ __('Status') }}</span>
                <x-status-badge :status="$option->is_active ? 'active' : 'inactive'" />
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-text-secondary">{{ __('Categoria') }}</span>
                <span class="text-sm text-text-primary">{{ $option->category?->name ?? '-' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-text-secondary">{{ __('Ordem') }}</span>
                <span class="text-sm text-text-primary">{{ $option->display_order }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-text-secondary">{{ __('Criado em') }}</span>
                <span class="text-sm text-text-primary">{{ $option->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-border dark:border-border-dark flex items-center gap-3">
            <a href="{{ route('menu-options.edit', $option) }}" class="btn-primary text-sm">
                <i class="fa-regular fa-pen-to-square text-sm"></i>
                {{ __('Editar') }}
            </a>
            <button type="button" x-on:click="if(confirm('{{ __("Excluir esta opcao?") }}')) document.getElementById('delete-option').submit()" class="btn-secondary text-sm">
                <i class="fa-solid fa-trash-can text-sm"></i>
                {{ __('Excluir') }}
            </button>
            <form id="delete-option" method="POST" action="{{ route('menu-options.destroy', $option) }}" class="hidden">
                @csrf @method('DELETE')
            </form>
        </div>
    </x-card>
</div>

@endsection
