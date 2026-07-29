@extends('layouts.app')
@section('title', 'Novo Restaurante')
@section('content')

<div class="max-w-3xl mx-auto">
    <div class="page-header flex items-center gap-3">
        <div class="section-icon">
            <i class="fa-solid fa-store text-xl"></i>
        </div>
        <div>
            <h1 class="page-title">Novo Restaurante</h1>
            <p class="page-subtitle">Cadastre a empresa e o administrador. O admin receberá um e-mail para finalizar o cadastro.</p>
        </div>
    </div>

    <x-card padding="6">
        <form method="POST" action="{{ route('root.restaurants.store') }}" class="space-y-6">
            @csrf

            <div>
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-building text-primary-600 text-sm"></i>
                    Dados da Empresa
                </h3>
                <div class="grid grid-cols-1 gap-5">
                    <x-input label="Razão Social" name="razao_social" type="text" placeholder="Ex: Restaurante Sabor Ltda" :value="old('razao_social')" required />
                    <p class="text-xs text-text-secondary -mt-3">A razão social é o nome jurídico da empresa. O admin poderá definir o nome fantasia depois.</p>
                </div>
            </div>

            <div class="border-t border-border dark:border-border-dark pt-6">
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-primary-600 text-sm"></i>
                    Administrador do Restaurante
                </h3>
                <p class="text-sm text-text-secondary dark:text-slate-400 mb-4">
                    Um usuário com perfil <strong>Admin</strong> será criado. Ele receberá um e-mail de convite para definir a senha e completar o cadastro do restaurante.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-input label="Nome do Admin" name="admin_name" type="text" placeholder="Ex: Maria Silva" :value="old('admin_name')" required />
                    <x-input label="E-mail do Admin" name="admin_email" type="email" placeholder="admin@restaurante.com" :value="old('admin_email')" required />
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-border dark:border-border-dark">
                <a href="{{ route('root.restaurants.index') }}" class="btn-ghost">Cancelar</a>
                <x-button variant="primary" size="lg">
                    <i class="fa-solid fa-paper-plane text-sm"></i>
                    Convidar Admin
                </x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection