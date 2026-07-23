@extends('layouts.app')
@section('title', __('Painel'))
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Painel') }}</h1>
        <p class="page-subtitle">{{ __('Resumo das operações de hoje') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 mb-8">
        <x-stat-card title="{{ __('Pedidos Hoje') }}" :value="$stats['orders_count'] ?? 0" color="primary"
            :trendValue="$stats['orders_yesterday'] > 0 ? round((($stats['orders_count'] - $stats['orders_yesterday']) / $stats['orders_yesterday']) * 100, 1) : 0">
            <x-slot:icon>
                <i class="fa-solid fa-cart-shopping text-2xl"></i>
            </x-slot>
        </x-stat-card>

        <x-stat-card title="{{ __('Faturamento Hoje') }}" :value="'R$ ' . number_format($stats['revenue'] ?? 0, 2, ',', '.')" color="green"
            :trendValue="$stats['revenue_yesterday'] > 0 ? round((($stats['revenue'] - $stats['revenue_yesterday']) / $stats['revenue_yesterday']) * 100, 1) : 0">
            <x-slot:icon>
                <i class="fa-solid fa-dollar-sign text-2xl"></i>
            </x-slot>
        </x-stat-card>

        <x-stat-card title="{{ __('Ticket Médio') }}" :value="'R$ ' . number_format($stats['average_ticket'] ?? 0, 2, ',', '.')" color="blue"
            :trendValue="$stats['average_ticket_yesterday'] > 0 ? round((($stats['average_ticket'] - $stats['average_ticket_yesterday']) / $stats['average_ticket_yesterday']) * 100, 1) : 0">
            <x-slot:icon>
                <i class="fa-solid fa-receipt text-2xl"></i>
            </x-slot>
        </x-stat-card>

        <x-stat-card title="{{ __('Pedidos Pendentes') }}" :value="$stats['pending_orders'] ?? 0" color="amber"
            :trendUp="($stats['pending_orders'] ?? 0) <= ($stats['pending_yesterday'] ?? 0)"
            :trend="($stats['pending_orders'] ?? 0) . ' ' . __('aguardando')">
            <x-slot:icon>
                <i class="fa-solid fa-clock text-2xl"></i>
            </x-slot>
        </x-stat-card>

        <x-stat-card title="{{ __('Cardápio Ativo') }}" :value="$stats['active_menu'] ? __('Publicado') : __('Nenhum')" color="purple"
            :trendUp="$stats['active_menu'] ?? false">
            <x-slot:icon>
                <i class="fa-solid fa-book text-2xl"></i>
            </x-slot>
        </x-stat-card>

        <x-stat-card title="{{ __('Clientes Atendidos') }}" :value="$stats['customers_served'] ?? 0" color="primary"
            :trendValue="$stats['customers_yesterday'] > 0 ? round((($stats['customers_served'] - $stats['customers_yesterday']) / $stats['customers_yesterday']) * 100, 1) : 0">
            <x-slot:icon>
                <i class="fa-solid fa-users text-2xl"></i>
            </x-slot>
        </x-stat-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2">
            <x-card padding="6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Receita x Pedidos') }}</h3>
                    <span class="text-xs text-text-secondary dark:text-slate-400">{{ __('Últimos 14 dias') }}</span>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </x-card>
        </div>

        <div>
            <x-card padding="6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Status dos Pedidos') }}</h3>
                </div>
                <div class="chart-container" style="height: 280px;">
                    <canvas id="statusDonut"></canvas>
                </div>
            </x-card>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div>
            <x-card padding="6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Pratos Mais Vendidos') }}</h3>
                </div>
                @if(count($topDishes) > 0)
                    <div class="space-y-3" id="topDishes">
                        @foreach($topDishes as $index => $dish)
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg {{ $index === 0 ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-text-primary dark:text-text-dark truncate">{{ $dish['dish_name'] }}</p>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 mt-1">
                                    <div class="bg-primary-500 h-1.5 rounded-full transition-all" style="width: {{ ($dish['total_qty'] / max($topDishes[0]['total_qty'], 1)) * 100 }}%"></div>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-text-primary dark:text-text-dark shrink-0">{{ $dish['total_qty'] }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-sm text-text-secondary">
                        {{ __('Nenhum prato vendido hoje.') }}
                    </div>
                @endif
            </x-card>
        </div>

        <div>
            <x-card padding="6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Últimos Pedidos') }}</h3>
                    <a href="{{ route('orders.index') }}" class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500">{{ __('Ver todos') }}</a>
                </div>
                @if(count($latestOrders) > 0)
                    <div class="space-y-2">
                        @foreach($latestOrders as $order)
                        <a href="{{ route('orders.show', $order['id']) }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors -mx-1">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-xs font-bold text-primary-600 dark:text-primary-400 shrink-0">
                                    #{{ $order['id'] }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-text-primary dark:text-text-dark truncate">{{ $order['customer']['name'] ?? __('Cliente Avulso') }}</p>
                                    <p class="text-xs text-text-secondary">R$ {{ number_format($order['total'], 2, ',', '.') }}</p>
                                </div>
                            </div>
                            <x-status-badge :status="$order['status']" />
                        </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-sm text-text-secondary">
                        {{ __('Nenhum pedido hoje.') }}
                    </div>
                @endif
            </x-card>
        </div>

        <div>
            <x-card padding="6">
                <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark mb-5">{{ __('Ações Rápidas') }}</h3>
                <div class="space-y-2">
                    <a href="{{ route('orders.create') }}"
                       class="flex items-center gap-3 p-3.5 rounded-xl bg-gradient-to-r from-primary-50 to-primary-100/50 dark:from-primary-900/20 dark:to-primary-900/10 border border-primary-200 dark:border-primary-800 text-primary-700 dark:text-primary-300 hover:from-primary-100 hover:to-primary-200 dark:hover:from-primary-900/30 dark:hover:to-primary-900/20 transition-all duration-150 group">
                        <div class="w-10 h-10 rounded-lg bg-primary-600 flex items-center justify-center text-white shadow-sm group-hover:shadow transition-shadow">
                            <i class="fa-solid fa-plus text-lg"></i>
                        </div>
                        <div>
                            <span class="text-sm font-semibold">{{ __('Novo Pedido') }}</span>
                            <p class="text-xs text-primary-600/70 dark:text-primary-400/70">{{ __('Criar novo pedido') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('daily-menus.create') }}"
                       class="flex items-center gap-3 p-3.5 rounded-xl bg-gradient-to-r from-green-50 to-green-100/50 dark:from-green-900/20 dark:to-green-900/10 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 hover:from-green-100 hover:to-green-200 dark:hover:from-green-900/30 dark:hover:to-green-900/20 transition-all duration-150 group">
                        <div class="w-10 h-10 rounded-lg bg-green-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fa-solid fa-clipboard-list text-lg"></i>
                        </div>
                        <div>
                            <span class="text-sm font-semibold">{{ __('Cardápio do Dia') }}</span>
                            <p class="text-xs text-green-600/70 dark:text-green-400/70">{{ __('Criar cardápio') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('orders.kanban') }}"
                       class="flex items-center gap-3 p-3.5 rounded-xl bg-gradient-to-r from-amber-50 to-amber-100/50 dark:from-amber-900/20 dark:to-amber-900/10 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 hover:from-amber-100 hover:to-amber-200 dark:hover:from-amber-900/30 dark:hover:to-amber-900/20 transition-all duration-150 group">
                        <div class="w-10 h-10 rounded-lg bg-amber-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fa-solid fa-kitchen-set text-lg"></i>
                        </div>
                        <div>
                            <span class="text-sm font-semibold">{{ __('Kanban da Cozinha') }}</span>
                            <p class="text-xs text-amber-600/70 dark:text-amber-400/70">{{ __('Gerenciar pedidos') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('reports.index') }}"
                       class="flex items-center gap-3 p-3.5 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100/50 dark:from-purple-900/20 dark:to-purple-900/10 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 hover:from-purple-100 hover:to-purple-200 dark:hover:from-purple-900/30 dark:hover:to-purple-900/20 transition-all duration-150 group">
                        <div class="w-10 h-10 rounded-lg bg-purple-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fa-solid fa-chart-simple text-lg"></i>
                        </div>
                        <div>
                            <span class="text-sm font-semibold">{{ __('Relatórios') }}</span>
                            <p class="text-xs text-purple-600/70 dark:text-purple-400/70">{{ __('Analisar dados') }}</p>
                        </div>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.08)' : 'rgba(0,0,0,0.05)';

    Chart.defaults.font.family = 'Inter';

    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['dates'] ?? []) !!},
                datasets: [
                    {
                        label: '{{ __('Receita (R$)') }}',
                        data: {!! json_encode($chartData['revenue'] ?? []) !!},
                        backgroundColor: 'rgba(234, 88, 12, 0.8)',
                        borderColor: '#ea580c',
                        borderWidth: 0,
                        borderRadius: 6,
                        yAxisID: 'y',
                        order: 2,
                    },
                    {
                        label: '{{ __('Pedidos') }}',
                        data: {!! json_encode($chartData['orders'] ?? []) !!},
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#059669',
                        pointBorderColor: isDark ? '#1e293b' : '#ffffff',
                        pointBorderWidth: 2,
                        borderWidth: 2,
                        yAxisID: 'y1',
                        type: 'line',
                        order: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, padding: 20, color: textColor, font: { size: 12 } }
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#ffffff',
                        titleColor: isDark ? '#f8fafc' : '#0f172a',
                        bodyColor: isDark ? '#cbd5e1' : '#475569',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let val = context.parsed.y;
                                if (label.includes('Receita')) {
                                    return label + ': R$ ' + val.toFixed(2).replace('.', ',');
                                }
                                return label + ': ' + val;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { size: 11 }, callback: function(v) { return 'R$' + v.toFixed(0); } },
                        title: { display: true, text: '{{ __('Receita (R$)') }}', color: textColor, font: { size: 11 } }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { color: textColor, font: { size: 11 } },
                        title: { display: true, text: '{{ __('Pedidos') }}', color: textColor, font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { size: 11 } }
                    }
                },
                animation: { duration: 400 }
            }
        });
    }

    const statusCtx = document.getElementById('statusDonut');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['{{ __('Recebido') }}', '{{ __('Em Preparo') }}', '{{ __('Saiu para Entrega') }}', '{{ __('Finalizado') }}', '{{ __('Cancelado') }}'],
                datasets: [{
                    data: [
                        {{ $statusDistribution['received'] ?? 0 }},
                        {{ $statusDistribution['preparing'] ?? 0 }},
                        {{ $statusDistribution['out_for_delivery'] ?? 0 }},
                        {{ $statusDistribution['completed'] ?? 0 }},
                        {{ $statusDistribution['canceled'] ?? 0 }},
                    ],
                    backgroundColor: ['#3b82f6', '#f59e0b', '#8b5cf6', '#22c55e', '#ef4444'],
                    borderColor: isDark ? '#1e293b' : '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 16, color: textColor, font: { size: 11 } }
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#ffffff',
                        titleColor: isDark ? '#f8fafc' : '#0f172a',
                        bodyColor: isDark ? '#cbd5e1' : '#475569',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' (' + pct + '%)';
                            }
                        }
                    }
                },
                animation: { duration: 400 }
            }
        });
    }
});
</script>
@endpush
@endsection
