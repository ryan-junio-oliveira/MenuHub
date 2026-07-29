@extends('layouts.guest')
@section('title', 'Completar Cadastro')
@section('content')

<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-2xl">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-text-primary dark:text-text-dark">Completar Cadastro</h1>
            <p class="text-text-secondary mt-1">Finalize as informações do restaurante e defina sua senha</p>
        </div>

        <x-card padding="6">
            <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-4 mb-6">
                <p class="text-sm text-primary-700 dark:text-primary-300">
                    <i class="fa-solid fa-building mr-1"></i>
                    <strong>{{ $restaurant->razao_social }}</strong>
                </p>
                <p class="text-xs text-primary-500 dark:text-primary-400 mt-1">
                    Admin: {{ $admin->email }}
                </p>
            </div>

            <form method="POST" action="{{ route('setup.complete', $token) }}" class="space-y-6">
                @csrf

                <div>
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-store text-primary-600 text-sm"></i>
                        Informações do Restaurante
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-input label="Nome Fantasia" name="name" type="text" placeholder="Ex: Restaurante Sabor" :value="old('name', $restaurant->name)" required />
                        <x-input label="Telefone" name="phone" type="text" placeholder="(11) 99999-9999" :value="old('phone', $restaurant->phone)" />
                        <div class="md:col-span-2">
                            <x-input-label for="address" value="Endereço" />
                            <textarea id="address" name="address" rows="2" placeholder="Rua, número, bairro, cidade, estado" class="input-field mt-1">{{ old('address', $restaurant->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="border-t border-border dark:border-border-dark pt-6">
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-lock text-primary-600 text-sm"></i>
                        Defina sua Senha
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-input label="Senha" name="password" type="password" placeholder="Mínimo 8 caracteres" required />
                        <x-input label="Confirmar Senha" name="password_confirmation" type="password" placeholder="Repita a senha" required />
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-border dark:border-border-dark">
                    <x-button variant="primary" size="lg">
                        <i class="fa-regular fa-circle-check text-sm"></i>
                        Finalizar Cadastro
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>

@endsection