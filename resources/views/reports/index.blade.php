@extends('layouts.app')
@section('title', __('Relatórios'))
@section('content')

<div class="max-w-7xl mx-auto" x-data="{ tab: 'revenue' }">
    <div class="page-header">
        <h1 class="page-title">{{ __('Relatórios') }}</h1>
        <p class="page-subtitle">{{ __('Analise o desempenho do seu restaurante') }}</p>
    </div>

    <div class="mb-6 border-b border-border dark:border-border-dark">
        <nav class="flex gap-6 overflow-x-auto" role="tablist">
            <button x-on:click="tab = 'revenue'" role="tab" :aria-selected="tab === 'revenue'"
                :class="{ 'border-primary-500 text-primary-600 dark:text-primary-400': tab === 'revenue', 'border-transparent text-text-secondary dark:text-slate-400 hover:text-text-primary dark:hover:text-text-dark': tab !== 'revenue' }"
                class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                <i class="fa-solid fa-dollar-sign text-sm mr-1.5"></i>
                {{ __('Receita') }}
            </button>
            <button x-on:click="tab = 'dishes'" role="tab" :aria-selected="tab === 'dishes'"
                :class="{ 'border-primary-500 text-primary-600 dark:text-primary-400': tab === 'dishes', 'border-transparent text-text-secondary dark:text-slate-400 hover:text-text-primary dark:hover:text-text-dark': tab !== 'dishes' }"
                class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                <i class="fa-solid fa-utensils text-sm mr-1.5"></i>
                {{ __('Pratos Mais Vendidos') }}
            </button>
            <button x-on:click="tab = 'combinations'" role="tab" :aria-selected="tab === 'combinations'"
                :class="{ 'border-primary-500 text-primary-600 dark:text-primary-400': tab === 'combinations', 'border-transparent text-text-secondary dark:text-slate-400 hover:text-text-primary dark:hover:text-text-dark': tab !== 'combinations' }"
                class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                <i class="fa-solid fa-list text-sm mr-1.5"></i>
                {{ __('Combinações') }}
            </button>
            <button x-on:click="tab = 'hours'" role="tab" :aria-selected="tab === 'hours'"
                :class="{ 'border-primary-500 text-primary-600 dark:text-primary-400': tab === 'hours', 'border-transparent text-text-secondary dark:text-slate-400 hover:text-text-primary dark:hover:text-text-dark': tab !== 'hours' }"
                class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                <i class="fa-regular fa-clock text-sm mr-1.5"></i>
                {{ __('Horários de Pico') }}
            </button>
            <button x-on:click="tab = 'demand'" role="tab" :aria-selected="tab === 'demand'"
                :class="{ 'border-primary-500 text-primary-600 dark:text-primary-400': tab === 'demand', 'border-transparent text-text-secondary dark:text-slate-400 hover:text-text-primary dark:hover:text-text-dark': tab !== 'demand' }"
                class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                <i class="fa-solid fa-chart-line text-sm mr-1.5"></i>
                {{ __('Previsão de Demanda') }}
            </button>
        </nav>
    </div>

    <div x-show="tab === 'revenue'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @include('reports.revenue')
    </div>
    <div x-show="tab === 'dishes'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @include('reports.dishes')
    </div>
    <div x-show="tab === 'combinations'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @include('reports.combinations')
    </div>
    <div x-show="tab === 'hours'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @include('reports.hours')
    </div>
    <div x-show="tab === 'demand'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @include('reports.demand')
    </div>
</div>

@endsection
