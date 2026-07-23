@props(['title', 'value', 'icon', 'trend' => null, 'trendUp' => null, 'trendValue' => null, 'color' => 'primary'])

@php
$colors = [
    'primary' => 'text-primary-600 bg-primary-50 dark:bg-primary-900/20 ring-primary-500/10',
    'green' => 'text-green-600 bg-green-50 dark:bg-green-900/20 ring-green-500/10',
    'blue' => 'text-blue-600 bg-blue-50 dark:bg-blue-900/20 ring-blue-500/10',
    'amber' => 'text-amber-600 bg-amber-50 dark:bg-amber-900/20 ring-amber-500/10',
    'purple' => 'text-purple-600 bg-purple-50 dark:bg-purple-900/20 ring-purple-500/10',
    'red' => 'text-red-600 bg-red-50 dark:bg-red-900/20 ring-red-500/10',
];
@endphp

<div class="stat-card relative overflow-hidden group">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-text-secondary dark:text-slate-400 truncate">{{ $title }}</p>
            <p class="mt-1.5 text-2xl font-semibold text-text-primary dark:text-text-dark tracking-tight">{{ $value }}</p>
            @if($trend && $trendValue !== null)
                <p class="mt-1.5 flex items-center gap-1 {{ floatval($trendValue) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    <i class="fa-solid {{ floatval($trendValue) >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} text-xs"></i>
                    <span class="text-xs font-medium">{{ floatval($trendValue) >= 0 ? '+' : '' }}{{ $trendValue }}%</span>
                    <span class="text-xs text-text-secondary dark:text-slate-500">{{ __('vs. ontem') }}</span>
                </p>
            @elseif($trend)
                <p class="mt-1 text-xs font-medium {{ $trendUp ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    <span>{{ $trendUp ? '↑' : '↓' }}</span> {{ $trend }}
                </p>
            @endif
        </div>
        @if($icon ?? false)
            <div class="flex-shrink-0 p-3 rounded-xl {{ $colors[$color] }} ring-1">
                {{ $icon }}
            </div>
        @endif
    </div>
    @if($slot ?? false)
        <div class="mt-3 pt-3 border-t border-border dark:border-border-dark">
            {{ $slot }}
        </div>
    @endif
</div>
