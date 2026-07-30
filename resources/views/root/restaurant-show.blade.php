@extends('layouts.app')
@section('title', $restaurant->name)
@section('content')

<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('root.restaurants.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i> {{ __('Voltar aos Restaurantes') }}
        </a>
    </div>

    <div class="page-header flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                @if ($restaurant->logo)
                <img src="{{ Storage::url($restaurant->logo) }}" alt="Logo" class="w-14 h-14 rounded-xl object-cover">
                @else
                <span class="text-xl font-bold text-primary-600 dark:text-primary-400">{{ substr($restaurant->name, 0, 2) }}</span>
                @endif
            </div>
            <div>
                <h1 class="page-title">{{ $restaurant->name }}</h1>
                <p class="page-subtitle">{{ $restaurant->email ?? $restaurant->phone ?? 'Sem contato' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if ($restaurant->setup_token && !$restaurant->isSetupComplete())
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-amber-500"></span>
                {{ __('Convite Pendente') }}
            </span>
            @endif
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $restaurant->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $restaurant->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                {{ $restaurant->is_active ? __('Ativo') : __('Inativo') }}
            </span>
            <form method="POST" action="{{ route('root.restaurants.toggle-active', $restaurant) }}" class="inline">
                @csrf @method('PUT')
                <button type="submit" class="btn-secondary text-sm" onclick="return confirm('{{ $restaurant->is_active ? 'Desativar' : 'Ativar' }} este restaurante?')">
                    {{ $restaurant->is_active ? __('Desativar') : __('Ativar') }}
                </button>
            </form>
            <form method="POST" action="{{ route('root.restaurants.destroy', $restaurant) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este restaurante? Todas as operacoes associadas serao removidas.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-secondary text-sm text-red-600 hover:text-red-700 dark:text-red-400">
                    <i class="fa-solid fa-trash-can text-sm mr-1"></i> Excluir
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="stat-card">
            <p class="stat-label">{{ __('Usuários') }}</p>
            <p class="stat-value">{{ $restaurant->users_count }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">{{ __('Clientes') }}</p>
            <p class="stat-value">{{ $restaurant->customers_count }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">{{ __('Pratos') }}</p>
            <p class="stat-value">{{ $restaurant->dishes_count }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">{{ __('Pedidos (mês)') }}</p>
            <p class="stat-value">{{ $monthOrders }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">{{ __('Receita') }}</p>
            <p class="stat-value text-green-600">R$ {{ number_format($revenue, 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2">
            <x-card padding="5">
                <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Informações') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Nome') }}</p>
                        <p class="text-sm font-medium">{{ $restaurant->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('E-mail') }}</p>
                        <p class="text-sm font-medium">{{ $restaurant->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Telefone') }}</p>
                        <p class="text-sm font-medium">{{ $restaurant->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('WhatsApp') }}</p>
                        <p class="text-sm font-medium">{{ $restaurant->whatsapp_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Chave PIX') }}</p>
                        <p class="text-sm font-medium font-mono">{{ $restaurant->pix_key ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Taxa de Entrega') }}</p>
                        <p class="text-sm font-medium">{{ $restaurant->delivery_fee ? 'R$ '.number_format($restaurant->delivery_fee, 2, ',', '.') : '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Endereço') }}</p>
                        <p class="text-sm font-medium">{{ $restaurant->address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('WhatsApp API') }}</p>
                        <p class="text-sm font-medium">{{ $restaurant->whatsapp_phone_id ? 'Configurado' : 'Não configurado' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Razão Social') }}</p>
                        <p class="text-sm font-medium">{{ $restaurant->razao_social ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Cadastro') }}</p>
                        <p class="text-sm font-medium">{{ $restaurant->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if (!$restaurant->isSetupComplete())
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Setup') }}</p>
                        @if ($restaurant->invitation_failed_at)
                            <p class="text-sm font-medium text-red-600 dark:text-red-400">
                                <i class="fa-regular fa-circle-xmark mr-1"></i>
                                Falha no envio do convite — {{ $restaurant->invitation_failed_at->format('d/m/Y H:i') }}
                            </p>
                        @elseif ($restaurant->invitation_sent_at)
                            <p class="text-sm font-medium text-amber-600 dark:text-amber-400">
                                <i class="fa-regular fa-clock mr-1"></i>
                                Convite enviado em {{ $restaurant->invitation_sent_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-xs text-text-secondary mt-0.5">Aguardando admin completar cadastro</p>
                        @else
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-400">
                                <i class="fa-regular fa-hourglass mr-1"></i>
                                Convite aguardando envio
                            </p>
                        @endif
                    </div>
                    @else
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Setup Completo') }}</p>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">
                            <i class="fa-regular fa-circle-check mr-1"></i>
                            {{ $restaurant->setup_completed_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
                @if ($restaurant->cover)
                <div class="mt-4">
                    <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Capa') }}</p>
                    <img src="{{ Storage::url($restaurant->cover) }}" alt="Capa" class="w-full h-32 rounded-lg object-cover">
                </div>
                @endif
            </x-card>
        </div>

        <div>
            <x-card padding="5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-text-primary dark:text-text-dark">{{ __('Usuários') }}</h3>
                    <a href="{{ route('root.users.create') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                        <i class="fa-solid fa-plus text-xs"></i> {{ __('Adicionar') }}
                    </a>
                </div>
                @if ($users->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($users as $user)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-surface dark:hover:bg-surface-dark transition-colors">
                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0">
                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ substr($user->name, 0, 2) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-text-primary truncate">{{ $user->name }}</p>
                            <p class="text-xs text-text-secondary truncate">{{ $user->email }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700' }}">
                            {{ $user->role === 'admin' ? 'Admin' : 'User' }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-text-secondary text-center py-6">{{ __('Nenhum usuário') }}</p>
                @endif
            </x-card>
        </div>
    </div>

    @if ($recentOrders->isNotEmpty())
    <x-card padding="5">
        <h3 class="text-base font-semibold text-text-primary dark:text-text-dark mb-4">{{ __('Últimos Pedidos') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">{{ __('Pedido') }}</th>
                        <th class="table-th">{{ __('Cliente') }}</th>
                        <th class="table-th">{{ __('Total') }}</th>
                        <th class="table-th">{{ __('Status') }}</th>
                        <th class="table-th">{{ __('Data') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @foreach ($recentOrders as $order)
                    <tr class="table-row">
                        <td class="table-td font-medium">#{{ $order->order_number ?? $order->id }}</td>
                        <td class="table-td">{{ $order->customer?->name ?? 'Avulso' }}</td>
                        <td class="table-td">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                        <td class="table-td"><x-status-badge :status="$order->status" /></td>
                        <td class="table-td text-sm text-text-secondary">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
    @endif
</div>

@endsection
