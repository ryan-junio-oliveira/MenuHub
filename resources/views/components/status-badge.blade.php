@props(['status' => 'received'])

@php
$badges = [
    'received' => 'badge-blue',
    'preparing' => 'badge-amber',
    'out_for_delivery' => 'badge-purple',
    'completed' => 'badge-green',
    'canceled' => 'badge-red',
    'active' => 'badge-green',
    'inactive' => 'badge-slate',
    'pending' => 'badge-amber',
    'paid' => 'badge-green',
    'published' => 'badge-green',
    'draft' => 'badge-slate',
];
$labels = [
    'received' => __('Recebido'),
    'preparing' => __('Em Preparo'),
    'out_for_delivery' => __('Saiu para Entrega'),
    'completed' => __('Finalizado'),
    'canceled' => __('Cancelado'),
    'active' => __('Ativo'),
    'inactive' => __('Inativo'),
    'pending' => __('Pendente'),
    'paid' => __('Pago'),
    'published' => __('Publicado'),
    'draft' => __('Rascunho'),
];
$class = $badges[$status] ?? 'badge-slate';
$label = $labels[$status] ?? ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>
    @if(in_array($status, ['received', 'preparing', 'out_for_delivery', 'pending']))
        <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
    @endif
    {{ $label }}
</span>
