@extends('layouts.app')
@section('title', __('Cardápios do Dia'))
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ __('Cardápios do Dia') }}</h1>
            <p class="page-subtitle">{{ __('Gerenciar seus cardápios diários') }}</p>
        </div>
        <a href="{{ route('daily-menus.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus text-sm"></i>
            {{ __('Criar Cardápio') }}
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <div class="xl:col-span-3">
            <x-card padding="6">
                <div x-data="calendar()" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-text-primary dark:text-text-dark" x-text="currentMonthName() + ' ' + currentYear()"></h3>
                        <div class="flex gap-1">
                            <button x-on:click="prevMonth()" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-text-secondary dark:text-slate-400 transition-colors duration-150" aria-label="Mês anterior">
                                <i class="fa-solid fa-chevron-left text-base"></i>
                            </button>
                            <button x-on:click="nextMonth()" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-text-secondary dark:text-slate-400 transition-colors duration-150" aria-label="Próximo mês">
                                <i class="fa-solid fa-chevron-right text-base"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-7 gap-px bg-border dark:bg-border-dark rounded-xl overflow-hidden">
                        <template x-for="day in ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']" :key="day">
                            <div class="bg-slate-50 dark:bg-slate-800/50 px-3 py-2.5 text-center text-xs font-semibold text-text-secondary dark:text-slate-400 uppercase tracking-wider" x-text="day"></div>
                        </template>
                        <template x-for="(cell, index) in calendarDays()" :key="index">
                            <div class="bg-card dark:bg-card-dark min-h-[110px] p-2.5 border-b border-border dark:border-border-dark transition-colors duration-150 hover:bg-slate-50 dark:hover:bg-slate-800/30"
                                :class="{
                                    'bg-primary-50/50 dark:bg-primary-900/10': cell.hasMenu,
                                    'text-slate-400 dark:text-slate-600': !cell.isCurrentMonth
                                }">
                                <div class="text-sm font-medium mb-1.5 text-text-primary dark:text-text-dark" x-text="cell.day"></div>
                                <template x-if="cell.hasMenu">
                                    <a href="#" class="block text-xs bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-lg px-2 py-1 truncate font-medium hover:bg-primary-200 dark:hover:bg-primary-900/70 transition-colors">
                                        {{ __('Cardápio ativo') }}
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </x-card>
        </div>

        <div>
            <x-card padding="6">
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Próximos Cardápios') }}</h3>
                <div class="space-y-3">
                    @forelse ($menus ?? [] as $menu)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-150">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-text-primary dark:text-text-dark truncate">{{ $menu->date instanceof \Carbon\Carbon ? $menu->date->format('d/m/Y') : $menu->date }}</p>
                            <p class="text-xs text-text-secondary dark:text-slate-400 mt-0.5">{{ $menu->items_count ?? 0 }} {{ __('itens') }}</p>
                        </div>
                        <div class="flex gap-2 flex-shrink-0 ml-3">
                            @if ($menu->is_published)
                            <form method="POST" action="{{ route('daily-menus.dispatch', $menu) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-medium text-green-600 dark:text-green-400 hover:text-green-500" title="{{ __('Enviar via WhatsApp') }}">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('daily-menus.edit', $menu) }}" class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300">{{ __('Editar') }}</a>
                            <a href="{{ route('daily-menus.show', $menu) }}" class="text-xs font-medium text-text-secondary dark:text-slate-400 hover:text-text-primary dark:hover:text-text-dark">{{ __('Ver') }}</a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10">
                        <i class="fa-regular fa-inbox text-2xl mx-auto text-slate-300 dark:text-slate-600 mb-3"></i>
                        <p class="text-sm text-text-secondary dark:text-slate-400">{{ __('Nenhum cardápio criado.') }}</p>
                    </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    function calendar() {
        return {
            currentDate: new Date(),
            prevMonth() {
                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
            },
            nextMonth() {
                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
            },
            currentMonthName() {
                return this.currentDate.toLocaleString('pt-BR', { month: 'long' });
            },
            currentYear() {
                return this.currentDate.getFullYear();
            },
            calendarDays() {
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth();
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const daysInPrevMonth = new Date(year, month, 0).getDate();
                const cells = [];
                for (let i = firstDay - 1; i >= 0; i--) {
                    cells.push({ day: daysInPrevMonth - i, isCurrentMonth: false, hasMenu: false });
                }
                for (let i = 1; i <= daysInMonth; i++) {
                    cells.push({ day: i, isCurrentMonth: true, hasMenu: i === new Date().getDate() });
                }
                const remaining = 42 - cells.length;
                for (let i = 1; i <= remaining; i++) {
                    cells.push({ day: i, isCurrentMonth: false, hasMenu: false });
                }
                return cells;
            }
        };
    }
</script>
@endpush
