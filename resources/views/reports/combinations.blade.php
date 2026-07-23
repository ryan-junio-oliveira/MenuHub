<x-card padding="5">
    <div class="flex items-center gap-2 mb-1">
        <i class="fa-solid fa-list text-base text-primary-600"></i>
        <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Combinações Populares') }}</h3>
    </div>
    <p class="text-sm text-text-secondary dark:text-slate-400 mb-4">{{ __('Pratos frequentemente pedidos juntos') }}</p>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-border dark:divide-border-dark">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Combinação') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Frequência') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border dark:divide-border-dark">
                @forelse ($combinations ?? [] as $i => $row)
                @php $rank = $i + 1; @endphp
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-4 py-3 text-sm text-text-secondary">{{ $rank }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-text-primary dark:text-text-dark">{{ $row->combination }}</td>
                    <td class="px-4 py-3 text-sm text-text-secondary">{{ $row->frequency }}</td>
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
