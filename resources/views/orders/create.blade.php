@extends('layouts.app')
@section('title', __('Novo Pedido'))
@section('content')

<div>
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary dark:hover:text-text-dark transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
            {{ __('Voltar aos Pedidos') }}
        </a>
    </div>

    <div class="page-header">
        <h1 class="page-title">{{ __('Novo Pedido') }}</h1>
        <p class="page-subtitle">{{ __('Criar um novo pedido para um cliente') }}</p>
    </div>

    <form method="POST" action="{{ route('orders.store') }}" x-data="orderForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card padding="5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-sm font-bold text-primary-600">1</div>
                        <h3 class="text-base font-semibold text-text-primary dark:text-text-dark">{{ __('Selecionar Cliente') }}</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-sm"></i>
                            <input type="text" x-model="customerSearch" x-on:input.debounce="searchCustomer" placeholder="{{ __('Buscar por nome ou telefone...') }}" class="input-field pl-9">
                        </div>
                        <div class="max-h-48 overflow-y-auto space-y-2" x-show="customerResults.length > 0">
                            <template x-for="customer in customerResults" :key="customer.id">
                                <div x-on:click="selectCustomer(customer)" class="flex items-center gap-3 p-3 rounded-lg border border-border dark:border-border-dark cursor-pointer hover:bg-surface dark:hover:bg-surface-dark transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-regular fa-user text-sm text-primary-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-text-primary dark:text-text-dark" x-text="customer.name"></p>
                                        <p class="text-xs text-text-secondary" x-text="customer.phone"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <template x-if="selectedCustomer">
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800">
                                <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-check text-sm text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-primary-800 dark:text-primary-200" x-text="selectedCustomer.name"></p>
                                    <p class="text-xs text-primary-600 dark:text-primary-400" x-text="selectedCustomer.phone"></p>
                                </div>
                                <button type="button" x-on:click="selectedCustomer = null; customerSearch = ''" class="text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            </div>
                        </template>
                        <input type="hidden" name="customer_id" x-bind:value="selectedCustomer?.id || ''">
                        <p class="text-xs text-text-secondary">{{ __('Pedidos podem ser feitos sem selecionar um cliente (Cliente Avulso).') }}</p>
                    </div>
                </x-card>

                <x-card padding="5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-sm font-bold text-primary-600">2</div>
                        <h3 class="text-base font-semibold text-text-primary dark:text-text-dark">{{ __('Selecionar Itens') }}</h3>
                    </div>
                    <div class="space-y-3">
                        @foreach ($menuItems ?? [] as $item)
                        @php $dish = $item->dish; @endphp
                        <div class="flex items-center gap-4 p-4 rounded-xl border border-border dark:border-border-dark bg-card dark:bg-card-dark hover:shadow-card-hover transition-shadow">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-text-primary dark:text-text-dark">{{ $dish->name }}</p>
                                <div class="mt-2">
                                    <select name="size[{{ $item->id }}]" class="input-field text-xs">
                                        <option value="small">{{ __('Pequeno') }} - R$ {{ number_format($dish->price_small, 2, ',', '.') }}</option>
                                        @if ($dish->price_medium)
                                        <option value="medium">{{ __('Médio') }} - R$ {{ number_format($dish->price_medium, 2, ',', '.') }}</option>
                                        @endif
                                        @if ($dish->price_large)
                                        <option value="large">{{ __('Grande') }} - R$ {{ number_format($dish->price_large, 2, ',', '.') }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" x-on:click="decreaseQty({{ $item->id }})" class="w-8 h-8 rounded-lg border border-border dark:border-border-dark flex items-center justify-center text-text-secondary hover:bg-surface dark:hover:bg-surface-dark hover:text-text-primary dark:hover:text-text-dark transition-colors">
                                    <i class="fa-solid fa-minus text-xs"></i>
                                </button>
                                <span class="w-8 text-center text-sm font-semibold text-text-primary dark:text-text-dark" x-text="qty[{{ $item->id }}] || 0"></span>
                                <button type="button" x-on:click="increaseQty({{ $item->id }})" class="w-8 h-8 rounded-lg bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 transition-colors">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card padding="5" class="sticky top-6">
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Resumo do Pedido') }}</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-surface dark:bg-surface-dark">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-user text-sm text-text-secondary"></i>
                                <span class="text-sm text-text-secondary">{{ __('Cliente') }}</span>
                            </div>
                            <span class="text-sm font-medium text-text-primary dark:text-text-dark" x-text="selectedCustomer ? selectedCustomer.name : 'Cliente Avulso'"></span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-lg bg-surface dark:bg-surface-dark">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-receipt text-sm text-text-secondary"></i>
                                <span class="text-sm text-text-secondary">{{ __('Itens') }}</span>
                            </div>
                            <span class="text-sm font-medium text-text-primary dark:text-text-dark" x-text="totalItems() + ' itens'"></span>
                        </div>

                        <div class="border-t border-border dark:border-border-dark pt-4">
                            <div class="flex justify-between text-sm font-semibold">
                                <span class="text-text-primary dark:text-text-dark">{{ __('Total Selecionado') }}</span>
                                <span class="text-text-primary dark:text-text-dark" x-text="totalItems()"></span>
                            </div>
                        </div>

                        <div>
                            <label class="input-label">{{ __('Observações') }}</label>
                            <textarea name="notes" rows="3" class="input-field min-h-[80px] mt-1" placeholder="{{ __('Instruções especiais...') }}">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="w-full">
                            <x-button variant="primary" size="lg" class="w-full">
                                <i class="fa-solid fa-plus text-lg"></i>
                                {{ __('Criar Pedido') }}
                            </x-button>
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</div>

@endsection
@push('scripts')
<script>
    function orderForm() {
        return {
            qty: {},
            selectedCustomer: null,
            customerSearch: '',
            customerResults: [],
            increaseQty(id) {
                this.qty[id] = (this.qty[id] || 0) + 1;
            },
            decreaseQty(id) {
                if (this.qty[id] > 0) this.qty[id]--;
            },
            totalItems() {
                return Object.values(this.qty).reduce((a, b) => a + b, 0);
            },
            searchCustomer() {
                if (this.customerSearch.length < 2) { this.customerResults = []; return; }
                fetch('{{ route("customers.search") }}?q=' + this.customerSearch)
                    .then(r => r.json()).then(d => { this.customerResults = d; });
            },
            selectCustomer(c) {
                this.selectedCustomer = c;
                this.customerSearch = c.name;
                this.customerResults = [];
            }
        };
    }
</script>
@endpush
