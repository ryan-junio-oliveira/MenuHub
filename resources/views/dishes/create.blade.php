@extends('layouts.app')
@section('title', __('Novo Prato'))
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Novo Prato') }}</h1>
        <p class="page-subtitle">{{ __('Adicionar um novo prato ao cardápio') }}</p>
    </div>

    <x-card padding="6">
        <form method="POST" action="{{ route('dishes.store') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <x-input
                label="{{ __('Nome do Prato') }}"
                name="name"
                type="text"
                placeholder="{{ __('Ex: Frango Grelhado') }}"
                :error="$errors->first('name')"
                value="{{ old('name') }}"
                required
            />

            <div>
                <x-input-label for="category_id" value="{{ __('Categoria') }}" />
                <select id="category_id" name="category_id" class="input-field mt-1">
                    <option value="">{{ __('Selecione uma categoria') }}</option>
                    @foreach ($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" value="{{ __('Descrição') }}" />
                <textarea id="description" name="description" rows="3" class="input-field min-h-[80px] mt-1" placeholder="{{ __('Descreva o prato') }}">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div>
                <x-input-label value="{{ __('Imagem do Prato') }}" />
                <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" class="input-field mt-1" />
                <p class="text-xs text-text-secondary mt-1">{{ __('PNG, JPG ou WEBP. Máx 2MB.') }}</p>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            <div class="border-t border-border dark:border-border-dark pt-4">
                <p class="text-sm font-medium text-text-primary dark:text-text-dark mb-3">{{ __('Preços por Tamanho') }}</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="price_small" value="{{ __('Pequeno (R$)') }}" />
                        <x-text-input id="price_small" name="price_small" type="number" step="0.01" class="mt-1 block w-full input-field" placeholder="{{ __('0,00') }}" :value="old('price_small')" />
                        <x-input-error :messages="$errors->get('price_small')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="price_medium" value="{{ __('Médio (R$)') }}" />
                        <x-text-input id="price_medium" name="price_medium" type="number" step="0.01" class="mt-1 block w-full input-field" placeholder="{{ __('0,00') }}" :value="old('price_medium')" />
                        <x-input-error :messages="$errors->get('price_medium')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="price_large" value="{{ __('Grande (R$)') }}" />
                        <x-text-input id="price_large" name="price_large" type="number" step="0.01" class="mt-1 block w-full input-field" placeholder="{{ __('0,00') }}" :value="old('price_large')" />
                        <x-input-error :messages="$errors->get('price_large')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="is_available" name="is_available" value="1" checked class="rounded border-border dark:border-border-dark text-primary-600 shadow-sm focus:ring-primary-500" />
                    <span class="text-sm text-text-primary dark:text-text-dark">{{ __('Disponível') }}</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="is_gourmet" name="is_gourmet" value="1" class="rounded border-border dark:border-border-dark text-amber-600 shadow-sm focus:ring-amber-500" />
                    <span class="text-sm text-text-primary dark:text-text-dark">{{ __('Gourmet / Adicional') }}</span>
                </label>
            </div>

            <div>
                <x-input-label for="max_selections" value="{{ __('Limite de Escolhas por Pedido') }}" />
                <x-text-input id="max_selections" name="max_selections" type="number" placeholder="Limite de escolhas" class="mt-1 block w-full input-field" :value="old('max_selections', 1)" />
                <x-input-error :messages="$errors->get('max_selections')" class="mt-2" />
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button variant="primary" size="md" type="submit">{{ __('Criar Prato') }}</x-button>
                <x-button variant="ghost" size="md" type="button" :href="route('dishes.index')">{{ __('Cancelar') }}</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
