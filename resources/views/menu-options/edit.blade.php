@extends('layouts.app')
@section('title', __('Editar Opcao'))
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Editar Opcao') }}</h1>
        <p class="page-subtitle">{{ $option->name }}</p>
    </div>

    <x-card padding="6">
        <form method="POST" action="{{ route('menu-options.update', $option) }}" class="space-y-6">
            @csrf @method('PUT')

            <x-input
                label="{{ __('Nome da Opcao') }}"
                name="name"
                type="text"
                placeholder="{{ __('Ex: Arroz Branco') }}"
                :error="$errors->first('name')"
                value="{{ old('name', $option->name) }}"
                required
            />

            <div>
                <x-input-label for="category_id" value="{{ __('Categoria') }}" />
                <select id="category_id" name="category_id" class="input-field mt-1">
                    <option value="">{{ __('Sem categoria') }}</option>
                    @foreach ($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $option->dish_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="display_order" value="{{ __('Ordem de Exibicao') }}" />
                <x-text-input id="display_order" name="display_order" type="number" min="0" class="mt-1 block w-full input-field" placeholder="{{ __('0') }}" :value="old('display_order', $option->display_order)" />
                <x-input-error :messages="$errors->get('display_order')" class="mt-2" />
            </div>

            <div class="flex items-center gap-6">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $option->is_active) ? 'checked' : '' }} class="rounded border-border dark:border-border-dark text-primary-600 shadow-sm focus:ring-primary-500" />
                    <span class="text-sm text-text-primary dark:text-text-dark">{{ __('Ativo') }}</span>
                </label>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button variant="primary" size="md" type="submit">{{ __('Salvar') }}</x-button>
                <x-button variant="ghost" size="md" type="button" :href="route('menu-options.index')">{{ __('Cancelar') }}</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
