@extends('layouts.app')
@section('title', __('Detalhes do Cliente'))
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">{{ __('Detalhes do Cliente') }}</h1>
            <p class="page-subtitle">{{ __('Perfil e histórico de pedidos') }}</p>
        </div>
            <div class="flex items-center gap-2">
                <x-button variant="secondary" size="sm" :href="route('customers.index')">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                    {{ __('Voltar') }}
                </x-button>
                <x-button variant="primary" size="sm" :href="route('customers.edit', $customer)">
                    <i class="fa-regular fa-pen-to-square text-sm"></i>
                    {{ __('Editar') }}
                </x-button>
                <form method="POST" action="{{ route('customers.anonymize', $customer) }}" class="inline" onsubmit="return confirm('Tem certeza? Os dados pessoais deste cliente serão anonimizados permanentemente.')">
                    @csrf @method('PUT')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border border-red-200 dark:border-red-800">
                        <i class="fa-regular fa-eye-slash text-sm"></i>
                        {{ __('Anonimizar Dados') }}
                    </button>
                </form>
            </div>
    </div>

    <x-card padding="6">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                <span class="text-xl font-bold text-primary-600 dark:text-primary-400">{{ substr($customer->name, 0, 2) }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-text-primary dark:text-text-dark">{{ $customer->name }}</h2>
                <p class="text-sm text-text-secondary">{{ __('Cliente desde') }} {{ $customer->created_at instanceof \Carbon\Carbon ? $customer->created_at->format('M/Y') : $customer->created_at }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-primary-50 dark:bg-primary-900/10 rounded-xl p-4 border border-primary-100 dark:border-primary-900/20">
                <div class="flex items-center gap-2 text-primary-600 dark:text-primary-400 mb-1">
                    <i class="fa-solid fa-phone text-sm"></i>
                    <span class="text-xs font-semibold uppercase tracking-wider">{{ __('Telefone') }}</span>
                </div>
                <p class="text-sm font-medium text-text-primary dark:text-text-dark">{{ $customer->phone ?? '-' }}</p>
            </div>
            <div class="bg-primary-50 dark:bg-primary-900/10 rounded-xl p-4 border border-primary-100 dark:border-primary-900/20">
                <div class="flex items-center gap-2 text-primary-600 dark:text-primary-400 mb-1">
                    <i class="fa-regular fa-envelope text-sm"></i>
                    <span class="text-xs font-semibold uppercase tracking-wider">{{ __('Email') }}</span>
                </div>
                <p class="text-sm font-medium text-text-primary dark:text-text-dark">{{ $customer->email ?? '-' }}</p>
            </div>
            <div class="bg-primary-50 dark:bg-primary-900/10 rounded-xl p-4 border border-primary-100 dark:border-primary-900/20">
                <div class="flex items-center gap-2 text-primary-600 dark:text-primary-400 mb-1">
                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                    <span class="text-xs font-semibold uppercase tracking-wider">{{ __('Total Pedidos') }}</span>
                </div>
                <p class="text-sm font-medium text-text-primary dark:text-text-dark">{{ $customer->orders_count ?? 0 }}</p>
            </div>
            <div class="bg-primary-50 dark:bg-primary-900/10 rounded-xl p-4 border border-primary-100 dark:border-primary-900/20">
                <div class="flex items-center gap-2 text-primary-600 dark:text-primary-400 mb-1">
                    <i class="fa-solid fa-dollar-sign text-sm"></i>
                    <span class="text-xs font-semibold uppercase tracking-wider">{{ __('Total Gasto') }}</span>
                </div>
                <p class="text-sm font-medium text-text-primary dark:text-text-dark">R$ {{ number_format($customer->total_spent ?? 0, 2, ',', '.') }}</p>
            </div>
        </div>

        @if ($customer->tags->isNotEmpty())
        <div class="mb-6">
            <p class="text-xs text-text-secondary uppercase tracking-wider mb-2">{{ __('Tags') }}</p>
            <div class="flex items-center gap-2 flex-wrap">
                @foreach ($customer->tags as $tag)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $tag->color }}"></span>
                    {{ $tag->name }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        @if ($customer->address)
        <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-border dark:border-border-dark">
            <div class="flex items-center gap-2 text-text-secondary mb-1">
                <i class="fa-solid fa-location-dot text-sm"></i>
                <span class="text-xs font-semibold uppercase tracking-wider">{{ __('Endereço') }}</span>
            </div>
            <p class="text-sm text-text-primary dark:text-text-dark">{{ $customer->address }}</p>
        </div>
        @endif

        @if ($customer->notes)
        <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-border dark:border-border-dark">
            <div class="flex items-center gap-2 text-text-secondary mb-1">
                <i class="fa-regular fa-file-lines text-sm"></i>
                <span class="text-xs font-semibold uppercase tracking-wider">{{ __('Observações') }}</span>
            </div>
            <p class="text-sm text-text-primary dark:text-text-dark">{{ $customer->notes }}</p>
        </div>
        @endif

        <div>
            <div class="flex items-center gap-2 mb-4">
                <i class="fa-solid fa-cart-shopping text-base text-text-secondary"></i>
                <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ __('Histórico de Pedidos') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border dark:divide-border-dark">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Pedido') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Data') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Itens') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Total') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border dark:divide-border-dark">
                        @forelse ($customer->orders as $order)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('orders.show', $order) }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500">#{{ $order->id }}</a>
                            </td>
                            <td class="px-4 py-3 text-sm text-text-secondary">{{ $order->created_at instanceof \Carbon\Carbon ? $order->created_at->format('d/m H:i') : $order->created_at }}</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">{{ $order->items_count ?? count($order->items) }}</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$order->status" />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center">
                                <x-empty-state
                                    title="{{ __('Nenhum pedido ainda') }}"
                                    description="{{ __('Este cliente ainda não fez nenhum pedido.') }}"
                                />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>
</div>

@endsection
