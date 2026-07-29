@extends('layouts.app')
@section('title', __('Editar Cliente'))
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Editar Cliente') }}</h1>
        <p class="page-subtitle">{{ __('Atualizar dados do cliente') }}</p>
    </div>

    <x-card padding="6">
        <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-input
                label="{{ __('Nome Completo') }}"
                name="name"
                type="text"
                placeholder="{{ __('Nome do cliente') }}"
                :error="$errors->first('name')"
                value="{{ old('name', $customer->name) }}"
                required
            />

            <x-input
                label="{{ __('Telefone') }}"
                name="phone"
                type="text"
                placeholder="{{ __('(11) 99999-9999') }}"
                :error="$errors->first('phone')"
                value="{{ old('phone', $customer->phone) }}"
                required
            />

            <x-input
                label="{{ __('E-mail') }}"
                name="email"
                type="email"
                placeholder="{{ __('cliente@exemplo.com') }}"
                :error="$errors->first('email')"
                value="{{ old('email', $customer->email) }}"
            />

            <div>
                <x-input-label for="address" value="{{ __('Endereço') }}" />
                <textarea id="address" name="address" rows="3" class="input-field min-h-[80px] mt-1" placeholder="{{ __('Endereço de entrega') }}">{{ old('address', $customer->address) }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="notes" value="{{ __('Observações') }}" />
                <textarea id="notes" name="notes" rows="2" class="input-field min-h-[60px] mt-1" placeholder="{{ __('Observações sobre o cliente') }}">{{ old('notes', $customer->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="tags" value="{{ __('Tags') }}" />
                <div class="mt-1.5 grid grid-cols-2 gap-2">
                    @foreach ($tags as $tag)
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-border dark:border-border-dark cursor-pointer hover:bg-surface dark:hover:bg-surface-dark transition-colors">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $customer->tags->pluck('id')->toArray())) ? 'checked' : '' }} class="rounded border-border text-primary-600 focus:ring-primary-500">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium" style="color: {{ $tag->color }}">
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $tag->color }}"></span>
                            {{ $tag->name }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button variant="primary" size="md" type="submit">{{ __('Atualizar Cliente') }}</x-button>
                <x-button variant="ghost" size="md" type="button" :href="route('customers.index')">{{ __('Cancelar') }}</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
