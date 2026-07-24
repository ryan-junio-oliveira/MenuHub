@extends('layouts.app')
@section('title', 'Perfil')
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">Perfil</h1>
        <p class="page-subtitle">Gerenciar suas informações pessoais</p>
    </div>

<div class="space-y-6">
    @include('profile.partials.update-profile-information-form')

    @include('profile.partials.update-password-form')

    @if (auth()->user()->role !== 'root')
    <div x-data="{ dangerOpen: false }" class="border border-red-200 dark:border-red-900 rounded-2xl overflow-hidden">
        <button type="button" @click="dangerOpen = !dangerOpen" class="w-full flex items-center justify-between px-6 py-4 bg-red-50 dark:bg-red-950/30 hover:bg-red-100 dark:hover:bg-red-950/50 transition-colors">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-base text-red-600"></i>
                <h3 class="text-lg font-semibold text-red-700 dark:text-red-400">Zona de Perigo</h3>
            </div>
            <i class="fa-solid fa-chevron-down text-red-500 transition-transform duration-200" :class="{ 'rotate-180': dangerOpen }"></i>
        </button>
        <div x-show="dangerOpen" x-cloak>
            @include('profile.partials.delete-user-form')
        </div>
    </div>
    @endif
</div>
</div>

@endsection
