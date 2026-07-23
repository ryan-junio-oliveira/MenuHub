@extends('layouts.app')
@section('title', __('Kanban da Cozinha'))
@section('content')

@php
$columns = [
    'received' => ['label' => __('Recebido'), 'color' => 'blue', 'border' => 'border-l-blue-500'],
    'preparing' => ['label' => __('Em Preparo'), 'color' => 'amber', 'border' => 'border-l-amber-500'],
    'out_for_delivery' => ['label' => __('Saiu para Entrega'), 'color' => 'purple', 'border' => 'border-l-purple-500'],
    'completed' => ['label' => __('Finalizado'), 'color' => 'green', 'border' => 'border-l-green-500'],
];
@endphp

    <div x-data="kanbanBoard()" x-init="init()">
        <div class="page-header flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="page-title">{{ __('Kanban da Cozinha') }}</h1>
                <p class="page-subtitle">{{ __('Arraste e solte os pedidos para atualizar o status') }}</p>
            </div>
        <div class="flex items-center gap-3">
            <button x-on:click="refreshOrders">
                <x-button variant="secondary" size="sm">
                    <i class="fa-solid fa-arrows-rotate text-sm"></i>
                    {{ __('Atualizar') }}
                </x-button>
            </button>
            <a href="{{ route('orders.index') }}">
                <x-button variant="secondary" size="sm">
                    <i class="fa-solid fa-table text-sm"></i>
                    {{ __('Tabela') }}
                </x-button>
            </a>
            <a href="{{ route('orders.create') }}">
                <x-button variant="primary" size="sm">
                    <i class="fa-solid fa-plus text-sm"></i>
                    {{ __('Novo Pedido') }}
                </x-button>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
        @foreach ($columns as $key => $col)
        <div class="kanban-column">
            <div class="flex items-center justify-between px-4 py-3.5 border-b border-border dark:border-border-dark {{ $col['border'] }} border-l-4 sticky top-0 bg-card dark:bg-card-dark z-10">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-{{ $col['color'] }}-500"></div>
                    <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark">{{ $col['label'] }}</h3>
                </div>
                <span class="inline-flex items-center justify-center min-w-[28px] h-6 px-2 rounded-full text-xs font-bold bg-{{ $col['color'] }}-100 text-{{ $col['color'] }}-700 dark:bg-{{ $col['color'] }}-900/30 dark:text-{{ $col['color'] }}-300 shadow-sm"
                      x-text="orders.filter(o => o.status === '{{ $key }}').length"></span>
            </div>
            <div class="flex-1 p-3 space-y-3 overflow-y-auto"
                x-on:dragover.prevent
                x-on:dragenter.prevent="$el.classList.add('bg-primary-50/50', 'dark:bg-primary-900/10')"
                x-on:dragleave.prevent="$el.classList.remove('bg-primary-50/50', 'dark:bg-primary-900/10')"
                x-on:drop="dropOrder($event, '{{ $key }}'); $el.classList.remove('bg-primary-50/50', 'dark:bg-primary-900/10')">

                <template x-for="order in orders.filter(o => o.status === '{{ $key }}')" :key="order.id">
                    <div class="kanban-card"
                        draggable="true"
                        x-on:dragstart="dragStart($event, order)"
                        x-on:dragend="dragEnd()"
                        :class="{ 'ring-2 ring-red-400 dark:ring-red-500': order.isUrgent, 'opacity-50': draggedOrder?.id === order.id }">

                        <div class="flex items-start justify-between mb-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-text-primary dark:text-text-dark truncate">#<span x-text="order.id"></span></p>
                                    <template x-if="order.payment_method === 'pix'">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 uppercase tracking-wider">PIX</span>
                                    </template>
                                </div>
                                <p class="text-xs text-text-secondary dark:text-slate-400 truncate mt-0.5" x-text="order.customer"></p>
                            </div>
                            <template x-if="order.isUrgent">
                                <span class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300 animate-pulse">
                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                    {{ __('Urgente') }}
                                </span>
                            </template>
                        </div>

                        <div class="space-y-1 mb-3">
                            <template x-for="item in order.items" :key="item.id">
                                <div class="flex justify-between text-xs text-text-secondary dark:text-slate-400">
                                    <span class="truncate" x-text="item.quantity + 'x ' + item.name"></span>
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-border dark:border-border-dark">
                            <div class="flex items-center gap-1.5 text-xs text-text-secondary dark:text-slate-400">
                                <i class="fa-regular fa-clock text-sm"></i>
                                <span x-text="timeAgo(order.created_at)"></span>
                            </div>
                            <span class="text-xs font-bold text-text-primary dark:text-text-dark">R$ <span x-text="order.total.toFixed(2).replace('.', ',')"></span></span>
                        </div>
                    </div>
                </template>

                <template x-if="orders.filter(o => o.status === '{{ $key }}').length === 0">
                    <div class="flex flex-col items-center justify-center h-32 text-sm text-text-secondary dark:text-slate-500">
                        <i class="fa-regular fa-inbox text-3xl mb-2 opacity-40"></i>
                        {{ __('Nenhum pedido') }}
                    </div>
                </template>
            </div>
        </div>
        @endforeach
    </div>
</div>

<p class="mt-4 text-xs text-text-secondary text-center">{{ __('Os pedidos são atualizados automaticamente a cada 30 segundos') }}</p>

@endsection
@push('scripts')
<script>
    function kanbanBoard() {
        return {
            orders: [],
            draggedOrder: null,
            init() {
                this.loadOrders();
                setInterval(() => this.loadOrders(), 30000);
            },
            async loadOrders() {
                try {
                    const res = await fetch('/api/orders/active');
                    const data = await res.json();
                    this.orders = data.map(o => ({
                        ...o,
                        isUrgent: o.is_urgent || (Date.now() - new Date(o.created_at).getTime() > 30 * 60 * 1000)
                    }));
                } catch (e) {}
            },
            refreshOrders() { this.loadOrders(); },
            dragStart(event, order) {
                this.draggedOrder = order;
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', order.id);
                setTimeout(() => event.target.classList.add('opacity-30'), 0);
            },
            dragEnd() {
                this.draggedOrder = null;
                document.querySelectorAll('.kanban-card').forEach(el => el.classList.remove('opacity-30'));
            },
            async dropOrder(event, newStatus) {
                if (!this.draggedOrder) return;
                const order = this.draggedOrder;
                const oldStatus = order.status;
                order.status = newStatus;
                this.draggedOrder = null;
                try {
                    const res = await fetch('/orders/' + order.id + '/status', {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ status: newStatus })
                    });
                    if (!res.ok) throw new Error();
                } catch (e) {
                    order.status = oldStatus;
                }
            },
            timeAgo(date) {
                const diff = Date.now() - new Date(date).getTime();
                const mins = Math.floor(diff / 60000);
                if (mins < 1) return '{{ __('agora') }}';
                if (mins < 60) return mins + '{{ __('min') }}';
                const hrs = Math.floor(mins / 60);
                return hrs + '{{ __('h') }} ' + (mins % 60) + '{{ __('min') }}';
            }
        };
    }
</script>
@endpush
