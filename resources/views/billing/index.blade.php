@extends('layouts.app')
@section('title', 'Cobranças')
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">Cobranças</h1>
            <p class="page-subtitle">Gerenciar assinaturas e cobranças dos restaurantes</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('root.billing.plans') }}" class="btn-secondary">
                <i class="fa-solid fa-tags text-sm"></i> Planos
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <div class="stat-card">
            <p class="stat-label">Total</p>
            <p class="stat-value">{{ $stats['total'] }}</p>
        </div>
        <div class="stat-card border-green-200 dark:border-green-800">
            <p class="stat-label text-green-600 dark:text-green-400">Ativas</p>
            <p class="stat-value text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="stat-card border-blue-200 dark:border-blue-800">
            <p class="stat-label text-blue-600 dark:text-blue-400">Trial</p>
            <p class="stat-value text-blue-600">{{ $stats['trial'] }}</p>
        </div>
        <div class="stat-card border-red-200 dark:border-red-800">
            <p class="stat-label text-red-600 dark:text-red-400">Expiradas</p>
            <p class="stat-value text-red-600">{{ $stats['expired'] }}</p>
        </div>
        <div class="stat-card border-amber-200 dark:border-amber-800">
            <p class="stat-label text-amber-600 dark:text-amber-400">Pendentes</p>
            <p class="stat-value text-amber-600">{{ $stats['pending_invoices'] }}</p>
        </div>
        <div class="stat-card border-purple-200 dark:border-purple-800">
            <p class="stat-label text-purple-600 dark:text-purple-400">Receita (mês)</p>
            <p class="stat-value text-purple-600">R$ {{ number_format($stats['monthly_revenue'], 2, ',', '.') }}</p>
        </div>
    </div>

    <x-card padding="0">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">Restaurante</th>
                        <th class="table-th">Plano</th>
                        <th class="table-th">Status</th>
                        <th class="table-th">Trial até</th>
                        <th class="table-th">Pago até</th>
                        <th class="table-th">Cobranças Pendentes</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @forelse ($restaurants as $restaurant)
                    <tr class="table-row">
                        <td class="table-td font-medium">{{ $restaurant->name }}</td>
                        <td class="table-td">{{ $restaurant->plan?->name ?? '-' }}</td>
                        <td class="table-td">
                            @php
                                $colors = [
                                    'trial' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                    'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                    'expired' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                    'canceled' => 'bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-300',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$restaurant->subscription_status] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($restaurant->subscription_status) }}
                            </span>
                        </td>
                        <td class="table-td text-sm">{{ $restaurant->trial_ends_at?->format('d/m/Y') ?? '-' }}</td>
                        <td class="table-td text-sm">{{ $restaurant->paid_until?->format('d/m/Y') ?? '-' }}</td>
                        <td class="table-td">
                            @if ($restaurant->pending_invoices > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                    {{ $restaurant->pending_invoices }}
                                </span>
                            @else
                                <span class="text-sm text-text-secondary">0</span>
                            @endif
                        </td>
                        <td class="table-td text-right">
                            <a href="{{ route('root.billing.restaurant', $restaurant) }}"
                               class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-500">
                                Gerenciar <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-text-secondary">Nenhum restaurante encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border dark:border-border-dark">
            {{ $restaurants->links() }}
        </div>
    </x-card>
</div>

@endsection
