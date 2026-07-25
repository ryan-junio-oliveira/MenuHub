@extends('layouts.app')
@section('title', 'Planos')
@section('content')

<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('root.billing.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i> Voltar às Cobranças
        </a>
    </div>

    <div class="page-header">
        <h1 class="page-title">Planos</h1>
        <p class="page-subtitle">Planos de assinatura disponíveis para os restaurantes</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($plans as $plan)
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl border border-border dark:border-border-dark shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-bold text-text-primary dark:text-text-dark">{{ $plan->name }}</h3>
                <p class="mt-2">
                    <span class="text-3xl font-bold text-text-primary dark:text-text-dark">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                    <span class="text-sm text-text-secondary">/mês</span>
                </p>
                <p class="mt-4 text-sm text-text-secondary">
                    <i class="fa-solid fa-users text-xs mr-1"></i> {{ $plan->max_users }} {{ $plan->max_users === 1 ? 'usuário' : 'usuários' }}
                    @if ($plan->max_orders_monthly > 0)
                    <br><i class="fa-solid fa-cart-shopping text-xs mr-1"></i> {{ $plan->max_orders_monthly }} pedidos/mês
                    @else
                    <br><i class="fa-solid fa-infinity text-xs mr-1"></i> Pedidos ilimitados
                    @endif
                </p>
                <ul class="mt-4 space-y-2">
                    @foreach ($plan->features ?? [] as $feature)
                    <li class="flex items-start gap-2 text-sm text-text-secondary">
                        <i class="fa-solid fa-check text-green-500 mt-0.5 text-xs"></i>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="px-6 py-3 bg-surface dark:bg-surface-dark border-t border-border dark:border-border-dark flex items-center justify-between">
                <span class="text-xs {{ $plan->is_active ? 'text-green-600' : 'text-red-600' }}">
                    {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                </span>
                <span class="text-xs text-text-secondary">{{ $plan->restaurants_count ?? 0 }} restaurantes</span>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
