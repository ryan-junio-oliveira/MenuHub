<x-card padding="5">
    <div class="flex items-center gap-2 mb-1">
        <i class="fa-regular fa-clock text-base text-primary-600"></i>
        <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Horários de Pico') }}</h3>
    </div>
    <p class="text-sm text-text-secondary dark:text-slate-400 mb-4">{{ __('Volume de pedidos por hora do dia') }}</p>

    @php
        $maxTotal = collect($peakHours)->max('total') ?: 1;
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <div class="chart-container" style="height: 300px;">
                <canvas id="hoursChart"></canvas>
            </div>
        </div>
        <div class="space-y-3">
            @forelse ($peakHours ?? [] as $h)
            @php $pct = round($h['total'] / $maxTotal * 100); @endphp
            <div>
                <div class="flex justify-between text-sm text-text-secondary dark:text-slate-400 mb-1">
                    <span class="font-medium text-text-primary dark:text-text-dark">{{ sprintf('%02d:00', $h['hour']) }}</span>
                    <span>{{ $h['total'] }} {{ __('pedidos') }}</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5">
                    <div class="bg-primary-500 h-2.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-text-secondary text-center py-8">{{ __('Nenhum dado disponível') }}</p>
            @endforelse
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-border dark:divide-border-dark">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Hora') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Pedidos') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('% do Total') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border dark:divide-border-dark">
                @forelse ($peakHours ?? [] as $h)
                @php $pct = $maxTotal > 0 ? round($h['total'] / $maxTotal * 100) : 0; @endphp
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-4 py-3 text-sm font-medium text-text-primary dark:text-text-dark">{{ sprintf('%02d:00', $h['hour']) }}</td>
                    <td class="px-4 py-3 text-sm text-text-secondary">{{ $h['total'] }}</td>
                    <td class="px-4 py-3 text-sm text-text-secondary">{{ $pct }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-12 text-center text-sm text-text-secondary">{{ __('Nenhum dado disponível') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('hoursChart');
        if (ctx) {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.08)' : 'rgba(0,0,0,0.05)';
            const data = @json($peakHours);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(h => String(h.hour).padStart(2, '0') + ':00'),
                    datasets: [{
                        label: '{{ __("Pedidos") }}',
                        data: data.map(h => h.total),
                        borderColor: '#ea580c',
                        backgroundColor: 'rgba(234, 88, 12, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ea580c',
                        pointBorderColor: isDark ? '#1e293b' : '#ffffff',
                        pointBorderWidth: 2,
                        borderWidth: 2,
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
                                    return context.parsed.y + ' {{ __("pedidos") }}';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: textColor, font: { size: 11 } },
                            title: { display: true, text: '{{ __("Pedidos") }}', color: textColor, font: { size: 11 } }
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
