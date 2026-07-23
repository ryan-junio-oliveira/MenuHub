@extends('layouts.app')
@section('title', __('Painel do Sistema'))
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Painel do Sistema') }}</h1>
        <p class="page-subtitle">{{ __('Visão geral de todos os restaurantes') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stat-card title="{{ __('Restaurantes') }}" :value="$restaurantCount" color="primary">
            <x-slot:icon>
                <i class="fa-solid fa-store text-2xl"></i>
            </x-slot>
        </x-stat-card>

        <x-stat-card title="{{ __('Usuários') }}" :value="$userCount" color="blue">
            <x-slot:icon>
                <i class="fa-solid fa-users text-2xl"></i>
            </x-slot>
        </x-stat-card>

        <x-stat-card title="{{ __('Total de Pedidos') }}" :value="$totalOrders" color="green">
            <x-slot:icon>
                <i class="fa-solid fa-cart-shopping text-2xl"></i>
            </x-slot>
        </x-stat-card>

        <x-stat-card title="{{ __('Receita Total') }}" :value="'R$ ' . number_format($totalRevenue, 2, ',', '.')" color="amber">
            <x-slot:icon>
                <i class="fa-solid fa-dollar-sign text-2xl"></i>
            </x-slot>
        </x-stat-card>
    </div>
</div>

@endsection
