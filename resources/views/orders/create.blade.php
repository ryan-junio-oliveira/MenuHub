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
                        <div class="flex items-center gap-4 p-4 rounded-xl border border-border dark:border-border-dark bg-card dark:bg-card-dark hover:shadow-card-hover transition-shadow"
                            x-data="{ selectedSize: 'small' }">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-text-primary dark:text-text-dark">{{ $dish->name }}</p>
                                <div class="mt-2">
                                    <select name="size[{{ $item->id }}]" x-model="selectedSize" x-on:change="updatePrices()" class="input-field text-xs">
                                        <option value="small" data-price="{{ $item->price_small ?? $dish->price_small }}">{{ __('Pequeno') }} - R$ {{ number_format($item->price_small ?? $dish->price_small, 2, ',', '.') }}</option>
                                        @if ($item->price_medium ?? $dish->price_medium)
                                        <option value="medium" data-price="{{ $item->price_medium ?? $dish->price_medium }}">{{ __('Médio') }} - R$ {{ number_format($item->price_medium ?? $dish->price_medium, 2, ',', '.') }}</option>
                                        @endif
                                        @if ($item->price_large ?? $dish->price_large)
                                        <option value="large" data-price="{{ $item->price_large ?? $dish->price_large }}">{{ __('Grande') }} - R$ {{ number_format($item->price_large ?? $dish->price_large, 2, ',', '.') }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" x-on:click="decreaseQty({{ $item->id }}); updatePrices()" class="w-8 h-8 rounded-lg border border-border dark:border-border-dark flex items-center justify-center text-text-secondary hover:bg-surface dark:hover:bg-surface-dark hover:text-text-primary dark:hover:text-text-dark transition-colors">
                                    <i class="fa-solid fa-minus text-xs"></i>
                                </button>
                                <span class="w-8 text-center text-sm font-semibold text-text-primary dark:text-text-dark" x-text="qty[{{ $item->id }}] || 0"></span>
                                <button type="button" x-on:click="increaseQty({{ $item->id }}); updatePrices()" class="w-8 h-8 rounded-lg bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 transition-colors">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                            <input type="hidden" :name="'items[' + {{ $item->id }} + '][dish_id]'" :value="{{ $dish->id }}">
                            <input type="hidden" :name="'items[' + {{ $item->id }} + '][daily_menu_item_id]'" :value="{{ $item->id }}">
                            <input type="hidden" :name="'items[' + {{ $item->id }} + '][dish_name]'" value="{{ $dish->name }}">
                            <input type="hidden" :name="'items[' + {{ $item->id }} + '][size]'" x-bind:value="selectedSize">
                            <input type="hidden" :name="'items[' + {{ $item->id }} + '][quantity]'" x-bind:value="qty[{{ $item->id }}] || 0">
                            <input type="hidden" :name="'items[' + {{ $item->id }} + '][unit_price]'" x-bind:value="getPrice({{ $item->id }}, selectedSize)">
                            <input type="hidden" :name="'items[' + {{ $item->id }} + '][subtotal]'" x-bind:value="(qty[{{ $item->id }}] || 0) * getPrice({{ $item->id }}, selectedSize)">
                        </div>
                        @endforeach
                    </div>
                </x-card>

                <x-card padding="5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-sm font-bold text-primary-600"><i class="fa-solid fa-truck text-sm"></i></div>
                        <h3 class="text-base font-semibold text-text-primary dark:text-text-dark">{{ __('Entrega e Pagamento') }}</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="delivery_type" value="{{ __('Tipo de Entrega') }}" />
                            <select id="delivery_type" name="delivery_type" x-model="deliveryType" x-on:change="updatePrices()" class="mt-1.5 block w-full input-field">
                                <option value="delivery">{{ __('Entrega') }}</option>
                                <option value="pickup">{{ __('Retirada no Local') }}</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="payment_method" value="{{ __('Forma de Pagamento') }}" />
                            <select id="payment_method" name="payment_method" x-model="paymentMethod" x-on:change="updatePrices()" class="mt-1.5 block w-full input-field">
                                <option value="pix">PIX</option>
                                <option value="cash">Dinheiro</option>
                                <option value="credit_card">Cartão de Crédito</option>
                                <option value="debit_card">Cartão de Débito</option>
                            </select>
                        </div>
                        <div class="md:col-span-2" x-show="deliveryType === 'delivery'">
                            <x-input-label for="delivery_address" value="{{ __('Endereço de Entrega') }}" />
                            <textarea id="delivery_address" name="delivery_address" rows="2" class="mt-1.5 block w-full input-field min-h-[60px]" placeholder="{{ __('Rua, número, bairro, complemento...') }}">{{ old('delivery_address') }}</textarea>
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card padding="5" class="sticky top-6">
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Resumo do Pedido') }}</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-surface dark:bg-surface-dark">
                            <span class="text-sm text-text-secondary">{{ __('Cliente') }}</span>
                            <span class="text-sm font-medium text-text-primary dark:text-text-dark" x-text="selectedCustomer ? selectedCustomer.name : 'Cliente Avulso'"></span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-lg bg-surface dark:bg-surface-dark">
                            <span class="text-sm text-text-secondary">{{ __('Itens') }}</span>
                            <span class="text-sm font-medium text-text-primary dark:text-text-dark" x-text="totalItems() + ' itens'"></span>
                        </div>

                        <div class="border-t border-border dark:border-border-dark pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-text-secondary">{{ __('Subtotal') }}</span>
                                <span class="font-medium text-text-primary dark:text-text-dark" x-text="'R$ ' + subtotal().toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div class="flex justify-between text-sm" x-show="deliveryType === 'delivery'">
                                <span class="text-text-secondary">{{ __('Taxa de Entrega') }}</span>
                                <span class="font-medium text-text-primary dark:text-text-dark" x-text="'R$ ' + deliveryFee().toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div class="flex justify-between text-sm font-bold border-t border-border dark:border-border-dark pt-2">
                                <span class="text-text-primary dark:text-text-dark">{{ __('Total') }}</span>
                                <span class="text-primary-600 dark:text-primary-400" x-text="'R$ ' + total().toFixed(2).replace('.', ',')"></span>
                            </div>
                        </div>

                        <input type="hidden" name="subtotal" x-bind:value="subtotal()">
                        <input type="hidden" name="delivery_fee" x-bind:value="deliveryFee()">
                        <input type="hidden" name="discount" value="0">
                        <input type="hidden" name="total" x-bind:value="total()">

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
            deliveryType: 'delivery',
            paymentMethod: 'pix',
            itemPrices: JSON.parse('{!! json_encode($menuItems->mapWithKeys(fn($item) => [$item->id => ["small" => ($item->price_small ?? $item->dish->price_small ?? 0), "medium" => ($item->price_medium ?? $item->dish->price_medium ?? null), "large" => ($item->price_large ?? $item->dish->price_large ?? null)]])->toArray() ?? []) !!}'),
            deliveryFeeValue: {{ ($restaurant->delivery_fee ?? 5) }},
            freeDeliveryMin: {{ ($restaurant->minimum_order ?? 0) }},
            increaseQty(id) {
                this.qty[id] = (this.qty[id] || 0) + 1;
            },
            decreaseQty(id) {
                if (this.qty[id] > 0) this.qty[id]--;
            },
            totalItems() {
                return Object.values(this.qty).reduce((a, b) => a + b, 0);
            },
            getPrice(itemId, size) {
                const prices = this.itemPrices[itemId];
                if (!prices) return 0;
                const price = prices[size] || prices['small'] || 0;
                return parseFloat(price);
            },
            subtotal() {
                let total = 0;
                for (const [id, qty] of Object.entries(this.qty)) {
                    if (qty > 0) {
                        const sel = document.querySelector(`select[name="size[${id}]"]`);
                        const size = sel ? sel.value : 'small';
                        total += qty * this.getPrice(parseInt(id), size);
                    }
                }
                return total;
            },
            deliveryFee() {
                if (this.deliveryType !== 'delivery') return 0;
                if (this.freeDeliveryMin > 0 && this.subtotal() >= this.freeDeliveryMin) return 0;
                return this.deliveryFeeValue;
            },
            total() {
                return this.subtotal() + this.deliveryFee();
            },
            updatePrices() {
            },
            searchCustomer() {
                if (this.customerSearch.length < 2) { this.customerResults = []; return; }
                fetch('{{ route("customers.search") }}?q=' + encodeURIComponent(this.customerSearch))
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
