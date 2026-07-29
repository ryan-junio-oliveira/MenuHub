@extends('layouts.app')
@section('title', __('Nova Categoria'))
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Nova Categoria') }}</h1>
        <p class="page-subtitle">{{ __('Adicionar uma nova categoria de pratos') }}</p>
    </div>

    <x-card padding="6">
        <form method="POST" action="{{ route('dish-categories.store') }}" class="space-y-6">
            @csrf

            <x-input
                label="{{ __('Nome da Categoria') }}"
                name="name"
                type="text"
                placeholder="{{ __('Ex: Pratos Principais') }}"
                :error="$errors->first('name')"
                value="{{ old('name') }}"
                required
            />

            <div>
                <x-input-label for="description" value="{{ __('Descrição') }}" />
                <textarea id="description" name="description" rows="3" class="input-field min-h-[80px] mt-1" placeholder="{{ __('Breve descrição desta categoria') }}">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked class="rounded border-border dark:border-border-dark text-primary-600 shadow-sm focus:ring-primary-500" />
                <span class="text-sm text-text-primary dark:text-text-dark">{{ __('Ativa') }}</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <x-button variant="primary" size="md" type="submit">{{ __('Criar Categoria') }}</x-button>
                <x-button variant="ghost" size="md" type="button" :href="route('dish-categories.index')">{{ __('Cancelar') }}</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
