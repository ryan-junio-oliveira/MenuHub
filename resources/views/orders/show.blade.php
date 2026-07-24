@extends('layouts.app')
@section('title', __('Pedido #') . $order->id)
@section('content')

<div>
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary dark:hover:text-text-dark transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
            {{ __('Voltar aos Pedidos') }}
        </a>
    </div>

    <div class="page-header flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <h1 class="page-title">{{ __('Pedido') }} #{{ $order->id }}</h1>
            <x-status-badge :status="$order->status" />
        </div>
        <div class="flex items-center gap-3">
            <button x-on:click="printThermal()" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <i class="fa-solid fa-print text-sm"></i>
                {{ __('Imprimir Comanda') }}
            </button>
            <p class="text-sm text-text-secondary">{{ $order->created_at instanceof \Carbon\Carbon ? $order->created_at->format('d/m/Y \à\s H:i') : $order->created_at }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <x-card padding="5" class="lg:col-span-2">
            <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark uppercase tracking-wider mb-4">{{ __('Itens do Pedido') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border dark:divide-border-dark">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('Prato') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('Tamanho') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('Qtd') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('Preço') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border dark:divide-border-dark">
                        @foreach ($order->items as $item)
                        <tr class="hover:bg-surface dark:hover:bg-surface-dark/50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-text-primary dark:text-text-dark">{{ $item->dish->name ?? $item->dish_name }}</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">{{ ucfirst($item->size) }}</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-text-primary dark:text-text-dark">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <div class="space-y-6">
            <x-card padding="5">
                <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark uppercase tracking-wider mb-3">{{ __('Cliente') }}</h3>
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-regular fa-user text-lg text-primary-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-text-primary dark:text-text-dark">{{ $order->customer->name ?? __('Cliente Avulso') }}</p>
                        @if ($order->customer)
                            <p class="text-xs text-text-secondary mt-0.5">{{ $order->customer->phone }}</p>
                        @endif
                    </div>
                </div>
            </x-card>

            <x-card padding="5">
                <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark uppercase tracking-wider mb-3">{{ __('Resumo do Pedido') }}</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">{{ __('Subtotal') }}</span>
                        <span class="text-text-primary dark:text-text-dark">R$ {{ number_format($order->subtotal ?? $order->total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">{{ __('Taxa de Entrega') }}</span>
                        <span class="text-text-primary dark:text-text-dark">R$ {{ number_format($order->delivery_fee ?? 0, 2, ',', '.') }}</span>
                    </div>
                    @if ($order->discount ?? false)
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">{{ __('Desconto') }}</span>
                        <span class="text-green-600 dark:text-green-400">-R$ {{ number_format($order->discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm font-semibold pt-2 border-t border-border dark:border-border-dark">
                        <span class="text-text-primary dark:text-text-dark">{{ __('Total') }}</span>
                        <span class="text-text-primary dark:text-text-dark">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>

                @if (($order->payment_method ?? '') === 'pix' && ($order->restaurant->pix_key ?? false))
                <div class="mt-4 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-qrcode text-green-600 dark:text-green-400 text-sm"></i>
                        <span class="text-sm font-semibold text-green-800 dark:text-green-200">{{ __('Pagamento PIX') }}</span>
                    </div>
                    <p class="text-xs text-green-700 dark:text-green-300 mb-2">{{ __('Chave PIX Copia e Cola:') }}</p>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 text-xs font-mono bg-white dark:bg-green-900/40 px-3 py-2 rounded-lg border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 truncate">{{ $order->restaurant->pix_key }}</code>
                        <button x-on:click="navigator.clipboard.writeText('{{ $order->restaurant->pix_key }}'); $el.innerHTML = '<i class=\'fa-solid fa-check\'></i>'; setTimeout(() => $el.innerHTML = '<i class=\'fa-regular fa-copy\'></i>', 2000)"
                            class="px-3 py-2 rounded-lg bg-green-600 text-white text-sm hover:bg-green-700 transition-colors">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>
                @endif
            </x-card>
        </div>
    </div>

    @if ($order->notes)
    <x-card padding="5" class="mb-6">
        <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark uppercase tracking-wider mb-2">{{ __('Observações') }}</h3>
        <p class="text-sm text-text-secondary">{{ $order->notes }}</p>
    </x-card>
    @endif

    <x-card padding="5">
        <h3 class="text-sm font-semibold text-text-primary dark:text-text-dark uppercase tracking-wider mb-4">{{ __('Atualizar Status') }}</h3>
        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="received">
                <x-button variant="secondary" size="sm" :disabled="$order->status === 'received'">
                    <i class="fa-regular fa-circle-check text-sm"></i>
                    {{ __('Recebido') }}
                </x-button>
            </form>
            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="preparing">
                <x-button variant="primary" size="sm" :disabled="$order->status === 'preparing'">
                    <i class="fa-solid fa-fire text-sm"></i>
                    {{ __('Em Preparo') }}
                </x-button>
            </form>
            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="out_for_delivery">
                <x-button variant="secondary" size="sm" :disabled="$order->status === 'out_for_delivery'">
                    <i class="fa-solid fa-truck text-sm"></i>
                    {{ __('Saiu para Entrega') }}
                </x-button>
            </form>
            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="completed">
                <x-button variant="success" size="sm" :disabled="$order->status === 'completed'">
                    <i class="fa-regular fa-circle-check text-sm"></i>
                    {{ __('Finalizar') }}
                </x-button>
            </form>
            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="canceled">
                <x-button variant="danger" size="sm" :disabled="$order->status === 'canceled'" x-on:click="if(!confirm('{{ __('Cancelar este pedido?') }}')) $event.preventDefault()">
                    <i class="fa-solid fa-xmark text-sm"></i>
                    {{ __('Cancelar Pedido') }}
                </x-button>
            </form>
        </div>
    </x-card>
</div>

@push('scripts')
<script>
    function printThermal() {
        const order = @json($order->load('items'));
        const restaurant = @json($order->restaurant);
        const width = 48;
        const d = '-'.repeat(width);
        const td = '='.repeat(width);
        const pad = (s, len) => String(s).padStart(len, ' ');
        const center = (s) => ' '.repeat(Math.floor((width - s.length) / 2)) + s;

        let lines = [];
        lines.push('');
        lines.push(center('COMANDA DE COZINHA'));
        lines.push(td);
        lines.push('Pedido: #' + order.order_number);
        lines.push('Data: ' + new Date(order.ordered_at || order.created_at).toLocaleString('pt-BR'));
        lines.push('Cliente: ' + (order.customer?.name || 'Avulso'));
        lines.push(d);

        if (order.delivery_type === 'delivery') {
            lines.push('Tipo: ENTREGA');
            if (order.delivery_address) lines.push('End: ' + order.delivery_address);
        } else {
            lines.push('Tipo: RETIRADA');
        }

        lines.push(d);
        lines.push('ITEM' + ' '.repeat(width - 15) + 'QTD  VALOR');
        lines.push(d);

        (order.items || []).forEach(item => {
            const name = item.dish_name + (item.size ? ' [' + item.size.charAt(0).toUpperCase() + ']' : '');
            const qty = String(item.quantity);
            const price = 'R$ ' + Number(item.unit_price).toFixed(2).replace('.', ',');
            const line = name.padEnd(width - 12) + qty.padStart(3) + '  ' + price.padStart(9);
            lines.push(line.substring(0, width));
        });

        lines.push(d);
        lines.push(pad('TOTAL: R$ ' + Number(order.total).toFixed(2).replace('.', ','), width));
        if (order.payment_method) {
            const labels = { pix: 'PIX', credit_card: 'Cartao Credito', debit_card: 'Cartao Debito', cash: 'Dinheiro' };
            lines.push(pad('Pagamento: ' + (labels[order.payment_method] || order.payment_method), width));
        }
        if (restaurant?.pix_key && order.payment_method === 'pix') {
            lines.push(pad('Chave PIX: ' + restaurant.pix_key, width));
        }
        if (order.customer_notes) {
            lines.push(d);
            lines.push('Obs: ' + order.customer_notes);
        }

        lines.push(td);
        lines.push(center('MenuHub - Comanda de Cozinha'));
        lines.push(center(new Date().toLocaleString('pt-BR')));
        lines.push('');

        const printWindow = window.open('', '_blank', 'width=400,height=600,menubar=no,toolbar=no');
        printWindow.document.write(`
            <html><head>
                <title>Comanda #${order.order_number}</title>
                <style>
                    @page { margin: 0; }
                    body { font-family: 'Courier New', monospace; font-size: 11px; white-space: pre; line-height: 1.2; padding: 8px; }
                </style>
            </head><body>
                <pre style="font-family:'Courier New',monospace;font-size:11px;">${lines.join('\n')}</pre>
                <script>window.onload=function(){window.print();setTimeout(()=>window.close(),1000)}<\/script>
            </body></html>
        `);
        printWindow.document.close();
    }
</script>
@endpush
@endsection
