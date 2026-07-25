@extends('layouts.app')
@section('title', __('Painel do Sistema'))
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ __('Painel do Sistema') }}</h1>
            <p class="page-subtitle">{{ __('Visão geral de todos os restaurantes') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('root.restaurants.index') }}" class="btn-secondary">
                <i class="fa-solid fa-store text-sm"></i>
                {{ __('Gerenciar Restaurantes') }}
            </a>
            <a href="{{ route('root.users') }}" class="btn-secondary">
                <i class="fa-solid fa-users text-sm"></i>
                {{ __('Gerenciar Usuários') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stat-card title="{{ __('Restaurantes') }}" :value="$restaurantCount" color="primary">
            <x-slot:icon><i class="fa-solid fa-store text-2xl"></i></x-slot>
        </x-stat-card>
        <x-stat-card title="{{ __('Usuários') }}" :value="$userCount" color="blue">
            <x-slot:icon><i class="fa-solid fa-users text-2xl"></i></x-slot>
        </x-stat-card>
        <x-stat-card title="{{ __('Total de Pedidos') }}" :value="$totalOrders" color="green">
            <x-slot:icon><i class="fa-solid fa-cart-shopping text-2xl"></i></x-slot>
        </x-stat-card>
        <x-stat-card title="{{ __('Receita Total') }}" :value="'R$ ' . number_format($totalRevenue, 2, ',', '.')" color="amber">
            <x-slot:icon><i class="fa-solid fa-dollar-sign text-2xl"></i></x-slot>
        </x-stat-card>
    </div>

    @if ($recentRestaurants->isNotEmpty())
    <x-card padding="5" class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-text-primary dark:text-text-dark">
                <i class="fa-solid fa-store text-primary-500 mr-2"></i>
                {{ __('Últimos Restaurantes') }}
            </h3>
            <a href="{{ route('root.restaurants.index') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500">
                {{ __('Ver todos') }} <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($recentRestaurants as $r)
            <a href="{{ route('root.restaurants.show', $r) }}" class="flex items-center gap-3 p-3 rounded-xl border border-border dark:border-border-dark hover:bg-surface dark:hover:bg-surface-dark transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ substr($r->name, 0, 2) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-text-primary dark:text-text-dark truncate group-hover:text-primary-600 transition-colors">{{ $r->name }}</p>
                    <p class="text-xs text-text-secondary">{{ $r->users_count ?? 0 }} {{ __('usuários') }} · {{ $r->created_at->diffForHumans() }}</p>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-text-secondary group-hover:text-primary-500 transition-colors"></i>
            </a>
            @endforeach
        </div>
    </x-card>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card padding="5">
            <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">
                <i class="fa-solid fa-bolt text-primary-500 mr-2"></i>
                {{ __('Ações Rápidas') }}
            </h3>
            <div class="space-y-3">
                <a href="{{ route('root.users.create') }}" class="flex items-center gap-3 p-3 rounded-xl border border-border dark:border-border-dark hover:bg-surface dark:hover:bg-surface-dark transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-plus text-sm text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-text-primary dark:text-text-dark group-hover:text-blue-600 transition-colors">{{ __('Criar Novo Usuário') }}</p>
                        <p class="text-xs text-text-secondary">{{ __('Adicione um admin ou usuário a um restaurante') }}</p>
                    </div>
                    <i class="fa-solid fa-arrow-right text-xs text-text-secondary group-hover:text-blue-500 transition-colors"></i>
                </a>
                <a href="{{ route('root.restaurants.index') }}" class="flex items-center gap-3 p-3 rounded-xl border border-border dark:border-border-dark hover:bg-surface dark:hover:bg-surface-dark transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-store text-sm text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-text-primary dark:text-text-dark group-hover:text-green-600 transition-colors">{{ __('Gerenciar Restaurantes') }}</p>
                        <p class="text-xs text-text-secondary">{{ __('Visualize e edite todos os restaurantes') }}</p>
                    </div>
                    <i class="fa-solid fa-arrow-right text-xs text-text-secondary group-hover:text-green-500 transition-colors"></i>
                </a>
                <a href="{{ route('root.users') }}" class="flex items-center gap-3 p-3 rounded-xl border border-border dark:border-border-dark hover:bg-surface dark:hover:bg-surface-dark transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-users-gear text-sm text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-text-primary dark:text-text-dark group-hover:text-purple-600 transition-colors">{{ __('Gerenciar Usuários') }}</p>
                        <p class="text-xs text-text-secondary">{{ __('Controle permissões de acesso') }}</p>
                    </div>
                    <i class="fa-solid fa-arrow-right text-xs text-text-secondary group-hover:text-purple-500 transition-colors"></i>
                </a>
            </div>
        </x-card>

        <x-card padding="5">
            <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">
                <i class="fa-solid fa-chart-simple text-primary-500 mr-2"></i>
                {{ __('Distribuição de Pedidos') }}
            </h3>
            <div class="space-y-4">
                @php
                    $statusConfig = [
                        'pending' => ['label' => 'Pendentes', 'color' => 'text-amber-500', 'bg' => 'bg-amber-100 dark:bg-amber-900/30'],
                        'received' => ['label' => 'Recebidos', 'color' => 'text-blue-500', 'bg' => 'bg-blue-100 dark:bg-blue-900/30'],
                        'preparing' => ['label' => 'Em Preparo', 'color' => 'text-purple-500', 'bg' => 'bg-purple-100 dark:bg-purple-900/30'],
                        'out_for_delivery' => ['label' => 'Saiu para Entrega', 'color' => 'text-indigo-500', 'bg' => 'bg-indigo-100 dark:bg-indigo-900/30'],
                        'completed' => ['label' => 'Finalizados', 'color' => 'text-green-500', 'bg' => 'bg-green-100 dark:bg-green-900/30'],
                        'canceled' => ['label' => 'Cancelados', 'color' => 'text-red-500', 'bg' => 'bg-red-100 dark:bg-red-900/30'],
                    ];
                    $maxVal = $statusCounts ? max(array_values($statusCounts)) : 0;
                @endphp
                @foreach ($statusConfig as $key => $cfg)
                    @php $count = $statusCounts[$key] ?? 0; @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-medium w-32 text-text-secondary">{{ __($cfg['label']) }}</span>
                        <div class="flex-1 h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $key === 'completed' ? 'bg-green-500' : ($key === 'canceled' ? 'bg-red-500' : 'bg-primary-500') }}" style="width: {{ $maxVal > 0 ? ($count / $maxVal) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-text-primary w-8 text-right">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</div>

@endsection
