@props(['order' => []])

<div class="bg-card dark:bg-card-dark rounded-lg border border-border dark:border-border-dark p-4 cursor-grab active:cursor-grabbing shadow-sm hover:shadow-card-hover transition-shadow duration-150"
    draggable="true"
    x-on:dragstart="dragStart($event, order)"
    :class="{ 'ring-2 ring-primary-500': order.isUrgent }">
    
    <div class="flex items-start justify-between mb-3">
        <div class="min-w-0">
            <p class="text-sm font-semibold text-text-primary dark:text-text-dark truncate">#{{ $order['order_number'] ?? $order['id'] }}</p>
            <p class="text-xs text-text-secondary dark:text-slate-400 truncate mt-0.5">{{ $order['customer'] ?? __('Walk-in') }}</p>
        </div>
        @if($order['isUrgent'] ?? false)
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                {{ __('Urgent') }}
            </span>
        @endif
    </div>

    <div class="space-y-1 mb-3">
        @foreach($order['items'] ?? [] as $item)
            <div class="flex justify-between text-xs text-text-secondary dark:text-slate-400">
                <span class="truncate">{{ $item['quantity'] ?? 1 }}x {{ $item['name'] ?? $item['dish_name'] ?? __('Item') }}</span>
            </div>
        @endforeach
    </div>

    <div class="flex items-center justify-between pt-3 border-t border-border dark:border-border-dark">
        <span class="text-xs text-text-secondary dark:text-slate-400">
            <span x-text="timeAgo('{{ $order['created_at'] ?? $order['ordered_at'] ?? now() }}')"></span>
        </span>
        <span class="text-xs font-semibold text-text-primary dark:text-text-dark">${{ number_format($order['total'] ?? 0, 2) }}</span>
    </div>
</div>
