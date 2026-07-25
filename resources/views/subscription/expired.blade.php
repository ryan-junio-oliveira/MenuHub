@extends('layouts.app')
@section('title', 'Assinatura Expirada')
@section('content')

<div class="min-h-[60vh] flex items-center justify-center">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-circle-exclamation text-4xl text-red-500"></i>
        </div>
        <h1 class="text-2xl font-bold text-text-primary dark:text-text-dark mb-2">Assinatura Expirada</h1>
        <p class="text-text-secondary mb-6">
            Sua assinatura está expirada ou foi cancelada. Entre em contato com o administrador para renovar seu plano e continuar usando o sistema.
        </p>
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="btn-primary">
            Sair
        </a>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>
    </div>
</div>

@endsection
