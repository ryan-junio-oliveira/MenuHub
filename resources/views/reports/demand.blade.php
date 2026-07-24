<div class="space-y-6">
    <x-card padding="5">
        <div class="flex items-center gap-2 mb-1">
            <i class="fa-solid fa-chart-line text-base text-primary-600"></i>
            <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Previsão de Demanda Semanal') }}</h3>
        </div>
        <p class="text-sm text-text-secondary dark:text-slate-400 mb-4">{{ __('Baseado nos últimos 4 semanas. Semana de') }} {{ $prediction['week_of'] ?? '' }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="p-4 rounded-xl bg-primary-50 dark:bg-primary-900/10 border border-primary-100 dark:border-primary-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400 mb-1">{{ __('Total Previsto') }}</p>
                <p class="text-2xl font-bold text-text-primary dark:text-text-dark">{{ $prediction['weekly_total_predicted'] ?? 0 }} {{ __('itens') }}</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-border dark:border-border-dark">
                <p class="text-xs font-semibold uppercase tracking-wider text-text-secondary mb-1">{{ __('Semana Anterior') }}</p>
                <p class="text-2xl font-bold text-text-primary dark:text-text-dark">{{ $prediction['last_week_total'] ?? 0 }} {{ __('itens') }}</p>
            </div>
            <div class="p-4 rounded-xl bg-{{ ($prediction['trend_pct'] ?? 0) >= 0 ? 'green' : 'red' }}-50 dark:bg-{{ ($prediction['trend_pct'] ?? 0) >= 0 ? 'green' : 'red' }}-900/10 border border-{{ ($prediction['trend_pct'] ?? 0) >= 0 ? 'green' : 'red' }}-100 dark:border-{{ ($prediction['trend_pct'] ?? 0) >= 0 ? 'green' : 'red' }}-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-{{ ($prediction['trend_pct'] ?? 0) >= 0 ? 'green' : 'red' }}-600 dark:text-{{ ($prediction['trend_pct'] ?? 0) >= 0 ? 'green' : 'red' }}-400 mb-1">{{ __('Tendência') }}</p>
                <p class="text-2xl font-bold text-{{ ($prediction['trend_pct'] ?? 0) >= 0 ? 'green' : 'red' }}-600 dark:text-{{ ($prediction['trend_pct'] ?? 0) >= 0 ? 'green' : 'red' }}-400">
                    {{ ($prediction['trend_pct'] ?? 0) >= 0 ? '+' : '' }}{{ $prediction['trend_pct'] ?? 0 }}%
                </p>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card padding="5" class="lg:col-span-2">
            <div class="flex items-center gap-2 mb-4">
                <i class="fa-solid fa-utensils text-base text-primary-600"></i>
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark">{{ __('Previsão por Prato') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border dark:divide-border-dark">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Prato') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Média/Dia') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Previsto Semana') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Dias Vendido') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border dark:divide-border-dark">
                        @forelse ($prediction['predictions'] ?? [] as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-text-primary dark:text-text-dark">{{ $item['dish_name'] }}</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">{{ $item['avg_per_day'] }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
                                    {{ $item['predicted_weekly'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-text-secondary">{{ $item['days_ordered'] }} {{ __('dias') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-sm text-text-secondary">{{ __('Nenhum dado disponível. Faça mais pedidos para gerar previsões.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card padding="5">
            <div class="flex items-center gap-2 mb-4">
                <i class="fa-regular fa-calendar text-base text-primary-600"></i>
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark">{{ __('Distribuição Semanal') }}</h3>
            </div>
            <p class="text-sm text-text-secondary dark:text-slate-400 mb-4">{{ __('Volume de pedidos por dia da semana') }}</p>
            <div class="space-y-3">
                @forelse ($prediction['day_multipliers'] ?? [] as $day)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-text-primary dark:text-text-dark font-medium">{{ $day['day'] }}</span>
                        <span class="text-text-secondary">{{ $day['orders'] }} {{ __('pedidos') }}</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-primary-500 h-2 rounded-full transition-all" style="width: {{ $day['pct_of_peak'] }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-text-secondary text-center py-8">{{ __('Sem dados') }}</p>
                @endforelse
            </div>
        </x-card>
    </div>
</div>
