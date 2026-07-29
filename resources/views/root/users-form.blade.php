@extends('layouts.app')
@section('title', $user ? 'Editar Usuário' : 'Novo Usuário')
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ $user ? 'Editar Usuário' : 'Novo Usuário' }}</h1>
        <p class="page-subtitle">{{ $user ? 'Atualizar dados do usuário' : 'Cadastrar novo usuário no sistema' }}</p>
    </div>

    <form method="POST" action="{{ $user ? route('root.users.update', $user) : route('root.users.store') }}">
        @csrf
        @if ($user)
        @method('PUT')
        @endif

        <x-card padding="6">
            <div class="space-y-6">
                <div>
                    <x-input-label for="name" value="Nome" />
                    <x-text-input id="name" name="name" type="text" placeholder="Nome do usuário" class="mt-1 block w-full input-field" :value="old('name', $user->name ?? '')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="E-mail" />
                    <x-text-input id="email" name="email" type="email" placeholder="E-mail do usuário" class="mt-1 block w-full input-field" :value="old('email', $user->email ?? '')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="{{ $user ? 'Nova Senha (deixe em branco para manter)' : 'Senha' }}" />
                    <x-text-input id="password" name="password" type="password" placeholder="Senha do usuário" class="mt-1 block w-full input-field" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="role" value="Função" />
                    <select id="role" name="role" class="mt-1 block w-full input-field" required>
                        <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="user" {{ old('role', $user->role ?? '') === 'user' ? 'selected' : '' }}>Usuário</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="restaurant_id" value="Restaurante" />
                    <select id="restaurant_id" name="restaurant_id" class="mt-1 block w-full input-field" required>
                        <option value="">Selecione um restaurante</option>
                        @foreach ($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}" {{ old('restaurant_id', $user->restaurant_id ?? '') == $restaurant->id ? 'selected' : '' }}>
                            {{ $restaurant->name }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('restaurant_id')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8">
                <x-button variant="primary" size="lg">
                    <i class="fa-regular fa-circle-check text-sm"></i>
                    {{ $user ? 'Atualizar' : 'Criar Usuário' }}
                </x-button>
                <a href="{{ route('root.users') }}" class="btn-secondary">Cancelar</a>
            </div>
        </x-card>
    </form>
</div>

@endsection
