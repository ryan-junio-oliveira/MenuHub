<div class="space-y-6">
    <x-card padding="5">
        <div class="flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-chart-simple text-base text-primary-600"></i>
            <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Visão Geral de Receita') }}</h3>
        </div>
        <div class="chart-container">
            <canvas id="revenueReportChart"></canvas>
        </div>
    </x-card>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @php
            $totalRevenue = collect($report)->sum('revenue');
            $totalOrders = collect($report)->sum('total_orders');
            $avgOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        @endphp
        <x-card padding="5">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-lg bg-primary-100 dark:bg-primary-900/30">
                    <i class="fa-solid fa-dollar-sign text-base text-primary-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-text-secondary dark:text-slate-400">{{ __('Total Período') }}</p>
                    <p class="mt-0.5 text-2xl font-bold text-text-primary dark:text-text-dark">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                </div>
            </div>
        </x-card>
        <x-card padding="5">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-lg bg-primary-100 dark:bg-primary-900/30">
                    <i class="fa-solid fa-arrow-trend-up text-base text-primary-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-text-secondary dark:text-slate-400">{{ __('Total Pedidos') }}</p>
                    <p class="mt-0.5 text-2xl font-bold text-text-primary dark:text-text-dark">{{ $totalOrders }}</p>
                </div>
            </div>
        </x-card>
        <x-card padding="5">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-lg bg-primary-100 dark:bg-primary-900/30">
            <i class="fa-solid fa-chart-simple text-base text-primary-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-text-secondary dark:text-slate-400">{{ __('Méd. Pedido') }}</p>
                    <p class="mt-0.5 text-2xl font-bold text-text-primary dark:text-text-dark">R$ {{ number_format($avgOrder, 2, ',', '.') }}</p>
                </div>
            </div>
        </x-card>
    </div>

    <x-card padding="5">
        <div class="flex items-center gap-2 mb-4">
            <i class="fa-solid fa-clipboard-list text-base text-primary-600"></i>
            <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Receita Mensal') }} - {{ $year }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border dark:divide-border-dark">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Mês') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Pedidos') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Receita') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Méd. Pedido') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @php $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']; @endphp
                    @foreach ($report ?? [] as $row)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium text-text-primary dark:text-text-dark">{{ $months[$row['month'] - 1] ?? $row['month'] }}</td>
                        <td class="px-4 py-3 text-sm text-text-secondary">{{ $row['total_orders'] }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-text-primary dark:text-text-dark">R$ {{ number_format($row['revenue'], 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-text-secondary">R$ {{ number_format($row['average_order'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('revenueReportChart');
        if (ctx) {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.08)' : 'rgba(0,0,0,0.05)';
            const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            const data = @json($report);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(r => months[r.month - 1]),
                    datasets: [{
                        label: '{{ __("Receita") }}',
                        data: data.map(r => r.revenue),
                        backgroundColor: 'rgba(234, 88, 12, 0.8)',
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
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
                                    return 'R$ ' + context.parsed.y.toFixed(2).replace('.', ',');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: textColor, font: { size: 11 }, callback: function(v) { return 'R$' + v.toFixed(0); } }
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
    });
</script>
@endpush
