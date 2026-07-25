@extends('layouts.app')
@section('title', $tag ? __('Editar Tag') : __('Nova Tag'))
@section('content')

<div class="max-w-lg mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ $tag ? __('Editar Tag') : __('Nova Tag') }}</h1>
        <p class="page-subtitle">{{ __('Crie ou edite tags para segmentar seus clientes') }}</p>
    </div>

    <x-card padding="6">
        <form method="POST" action="{{ $tag ? route('customer-tags.update', $tag) : route('customer-tags.store') }}" class="space-y-6">
            @csrf
            @if ($tag) @method('PUT') @endif

            <x-input
                label="{{ __('Nome da Tag') }}"
                name="name"
                type="text"
                placeholder="{{ __('Ex: VIP, Novo, Fiel, Vegano') }}"
                :value="old('name', $tag->name ?? '')"
                required
            />

            <div>
                <x-input-label for="color" value="{{ __('Cor') }}" />
                <div class="flex items-center gap-3 mt-1.5">
                    <input type="color" id="color" name="color" value="{{ old('color', $tag->color ?? '#6366f1') }}" class="w-10 h-10 rounded-lg border border-border dark:border-border-dark cursor-pointer" />
                    <input type="text" name="color" value="{{ old('color', $tag->color ?? '#6366f1') }}" class="input-field flex-1 font-mono" maxlength="7" required />
                </div>
                <x-input-error :messages="$errors->get('color')" class="mt-2" />
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button variant="primary" size="md" type="submit">
                    {{ $tag ? __('Atualizar Tag') : __('Criar Tag') }}
                </x-button>
                <a href="{{ route('customer-tags.index') }}" class="btn-ghost">{{ __('Cancelar') }}</a>
            </div>
        </form>
    </x-card>
</div>

@endsection
