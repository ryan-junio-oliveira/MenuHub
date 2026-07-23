@extends('layouts.app')
@section('title', __('Editar Prato'))
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Editar Prato') }}</h1>
        <p class="page-subtitle">{{ __('Atualizar informações do prato') }}</p>
    </div>

    <x-card padding="6">
        <form method="POST" action="{{ route('dishes.update', $dish) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-input
                label="{{ __('Nome do Prato') }}"
                name="name"
                type="text"
                placeholder="{{ __('Ex: Frango Grelhado') }}"
                :error="$errors->first('name')"
                value="{{ old('name', $dish->name) }}"
                required
            />

            <div>
                <x-input-label for="category_id" value="{{ __('Categoria') }}" />
                <select id="category_id" name="category_id" class="input-field mt-1">
                    <option value="">{{ __('Selecione uma categoria') }}</option>
                    @foreach ($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ (old('category_id', $dish->category_id) == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" value="{{ __('Descrição') }}" />
                <textarea id="description" name="description" rows="3" class="input-field min-h-[80px] mt-1" placeholder="{{ __('Descreva o prato') }}">{{ old('description', $dish->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <x-input
                label="{{ __('Preço (R$)') }}"
                name="price"
                type="number"
                step="0.01"
                placeholder="{{ __('0,00') }}"
                :error="$errors->first('price')"
                value="{{ old('price', $dish->price) }}"
                required
            />

            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="is_available" name="is_available" value="1" {{ $dish->is_available ? 'checked' : '' }} class="rounded border-border dark:border-border-dark text-primary-600 shadow-sm focus:ring-primary-500" />
                <span class="text-sm text-text-primary dark:text-text-dark">{{ __('Disponível') }}</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <x-button variant="primary" size="md" type="submit">{{ __('Atualizar Prato') }}</x-button>
                <x-button variant="ghost" size="md" :href="route('dishes.index')">{{ __('Cancelar') }}</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
