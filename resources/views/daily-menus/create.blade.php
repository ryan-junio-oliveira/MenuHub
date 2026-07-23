@extends('layouts.app')
@section('title', __('Criar Cardápio'))
@section('content')

<div class="max-w-5xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">{{ __('Criar Cardápio do Dia') }}</h1>
        <p class="page-subtitle">{{ __('Monte um novo cardápio selecionando pratos e definindo preços') }}</p>
    </div>

    <form method="POST" action="{{ route('daily-menus.store') }}" x-data="menuBuilder()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card padding="6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="date" value="{{ __('Data do Cardápio') }}" />
                            <x-text-input id="date" name="date" type="date" class="mt-1.5 block w-full input-field" :value="old('date')" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="max_selections" value="{{ __('Máx. Seleções por Pedido') }}" />
                            <x-text-input id="max_selections" name="max_selections" type="number" class="mt-1.5 block w-full input-field" :value="old('max_selections', 3)" required />
                            <x-input-error :messages="$errors->get('max_selections')" class="mt-2" />
                        </div>
                    </div>
                </x-card>

                <x-card padding="6">
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-1">{{ __('Selecionar Pratos') }}</h3>
                    <p class="text-sm text-text-secondary dark:text-slate-400 mb-5">{{ __('Marque os pratos para incluir e defina os preços para cada tamanho.') }}</p>

                    <div id="dishes-container" class="space-y-3">
                        @foreach ($categories ?? [] as $category)
                        <div class="rounded-xl border border-border dark:border-border-dark overflow-hidden">
                            <div x-on:click="toggleCategory({{ $category->id }})" class="flex items-center justify-between px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-150">
                                <h4 class="text-sm font-semibold text-text-primary dark:text-text-dark">{{ $category->name }}</h4>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-text-secondary dark:text-slate-400" x-text="selectedInCategory({{ $category->id }})"></span>
                                    <i class="fa-solid fa-chevron-down text-base text-text-secondary dark:text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openCategories.includes({{ $category->id }}) }"></i>
                                </div>
                            </div>
                            <div x-show="openCategories.includes({{ $category->id }})" class="divide-y divide-border dark:divide-border-dark">
                                @foreach ($category->dishes as $dish)
                                @php
                                    $dishId = $dish->id;
                                    $dishName = $dish->name;
                                    $dishDesc = $dish->description ?? '';
                                @endphp
                                <div class="px-5 py-4 flex items-start gap-4 hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors duration-150">
                                    <input type="checkbox" name="dishes[]" value="{{ $dishId }}"
                                        x-on:change="toggleDish({{ $dishId }}, $event.target.checked)"
                                        class="mt-1 h-4 w-4 rounded border-border dark:border-border-dark text-primary-600 focus:ring-primary-500">
                                    <div class="flex-1 min-w-0">
                                        <label class="text-sm font-medium text-text-primary dark:text-text-dark cursor-pointer">{{ $dishName }}</label>
                                        @if($dishDesc)
                                        <p class="text-xs text-text-secondary dark:text-slate-400 mt-0.5">{{ $dishDesc }}</p>
                                        @endif
                                        <div class="mt-3 grid grid-cols-3 gap-3" x-show="selectedDishes.includes({{ $dishId }})" x-cloak>
                                            <div>
                                                <label class="block text-xs font-medium text-text-secondary dark:text-slate-400 mb-1">{{ __('Pequeno') }}</label>
                                                <input type="number" name="price_small[{{ $dishId }}]" step="0.01" placeholder="{{ __('R$ 0,00') }}"
                                                    class="input-field text-sm"
                                                    x-on:input="calculateTotal()" />
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-text-secondary dark:text-slate-400 mb-1">{{ __('Médio') }}</label>
                                                <input type="number" name="price_medium[{{ $dishId }}]" step="0.01" placeholder="{{ __('R$ 0,00') }}"
                                                    class="input-field text-sm"
                                                    x-on:input="calculateTotal()" />
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-text-secondary dark:text-slate-400 mb-1">{{ __('Grande') }}</label>
                                                <input type="number" name="price_large[{{ $dishId }}]" step="0.01" placeholder="{{ __('R$ 0,00') }}"
                                                    class="input-field text-sm"
                                                    x-on:input="calculateTotal()" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-card>
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-6 space-y-4">
                    <x-card padding="6">
                        <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-list text-base text-primary-600"></i>
                            {{ __('Itens Selecionados') }}
                        </h3>
                        <template x-if="selectedDishes.length === 0">
                            <div class="text-center py-8">
                                <i class="fa-solid fa-plus text-2xl mx-auto text-slate-300 dark:text-slate-600 mb-3"></i>
                                <p class="text-sm text-text-secondary dark:text-slate-400">{{ __('Nenhum prato selecionado') }}</p>
                                <p class="text-xs text-text-secondary dark:text-slate-500 mt-1">{{ __('Selecione pratos das categorias') }}</p>
                            </div>
                        </template>
                        <template x-for="dishId in selectedDishes" :key="dishId">
                            <div class="flex items-center justify-between py-2.5 border-b border-border dark:border-border-dark last:border-0">
                                <span class="text-sm font-medium text-text-primary dark:text-text-dark truncate" x-text="getDishName(dishId)"></span>
                                <button type="button" x-on:click="removeDish(dishId)" class="text-xs font-medium text-red-500 hover:text-red-600 flex-shrink-0 ml-2">{{ __('Remover') }}</button>
                            </div>
                        </template>
                        <div class="mt-5 pt-4 border-t border-border dark:border-border-dark">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-text-secondary dark:text-slate-400">{{ __('Total selecionado') }}</span>
                                <span class="font-semibold text-text-primary dark:text-text-dark" x-text="selectedDishes.length + ' prato' + (selectedDishes.length !== 1 ? 's' : '')"></span>
                            </div>
                        </div>
                        <div class="mt-6 space-y-3">
                            <button type="submit" class="btn-primary w-full">
                                <i class="fa-regular fa-circle-check text-sm"></i>
                                {{ __('Criar Cardápio') }}
                            </button>
                            <a href="{{ route('daily-menus.index') }}" class="btn-secondary w-full justify-center">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
@push('scripts')
<script>
    function menuBuilder() {
        return {
            selectedDishes: [],
            openCategories: [],
            dishNames: JSON.parse('{!! json_encode($categories?->flatMap(fn($c) => $c->dishes->mapWithKeys(fn($d) => [$d->id => $d->name]))?->toArray() ?? []) !!}'),
            categoryDishes: JSON.parse('{!! json_encode($categories?->mapWithKeys(fn($c) => [$c->id => $c->dishes->pluck('id')])?->toArray() ?? []) !!}'),
            toggleCategory(id) {
                const idx = this.openCategories.indexOf(id);
                if (idx > -1) {
                    this.openCategories.splice(idx, 1);
                } else {
                    this.openCategories.push(id);
                }
            },
            toggleDish(id, checked) {
                if (checked) {
                    if (!this.selectedDishes.includes(id)) {
                        this.selectedDishes.push(id);
                    }
                } else {
                    this.selectedDishes = this.selectedDishes.filter(d => d !== id);
                }
            },
            removeDish(id) {
                this.selectedDishes = this.selectedDishes.filter(d => d !== id);
                const checkbox = document.querySelector(`input[name="dishes[]"][value="${id}"]`);
                if (checkbox) checkbox.checked = false;
            },
            getDishName(id) {
                return this.dishNames[id] || '{{ __("Prato") }} #' + id;
            },
            selectedInCategory(categoryId) {
                const dishIds = this.categoryDishes[categoryId] || [];
                const count = dishIds.filter(id => this.selectedDishes.includes(id)).length;
                return count ? count + ' {{ __("selecionado") }}' + (count !== 1 ? 's' : '') : '';
            },
            calculateTotal() {
                return this.selectedDishes.length;
            }
        };
    }
</script>
@endpush
