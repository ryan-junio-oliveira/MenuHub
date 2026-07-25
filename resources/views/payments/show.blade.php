@extends('layouts.app')
@section('title', __('Pagamento #') . $payment->id)
@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i> {{ __('Voltar aos Pagamentos') }}
        </a>
    </div>

    <div class="page-header flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h1 class="page-title">{{ __('Pagamento') }} #{{ $payment->id }}</h1>
            @php
            $statusClasses = ['pending' => 'bg-amber-50 text-amber-700', 'completed' => 'bg-green-50 text-green-700', 'failed' => 'bg-red-50 text-red-700', 'refunded' => 'bg-purple-50 text-purple-700'];
            $statusLabels = ['pending' => 'Pendente', 'completed' => 'Confirmado', 'failed' => 'Falha', 'refunded' => 'Reembolsado'];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClasses[$payment->status] ?? '' }}">
                {{ $statusLabels[$payment->status] ?? $payment->status }}
            </span>
        </div>
    </div>

    <x-card padding="6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Pedido') }}</p>
                <p class="text-sm font-medium">#{{ $payment->order->order_number ?? $payment->order_id }}</p>
            </div>
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Cliente') }}</p>
                <p class="text-sm font-medium">{{ $payment->order?->customer?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Método') }}</p>
                <p class="text-sm font-medium">{{ ['pix' => 'PIX', 'credit_card' => 'Cartão de Crédito', 'debit_card' => 'Cartão de Débito', 'cash' => 'Dinheiro'][$payment->method] ?? $payment->method }}</p>
            </div>
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Valor') }}</p>
                <p class="text-lg font-bold text-primary-600">R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
            </div>
            @if ($payment->transaction_id)
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Transação') }}</p>
                <p class="text-sm font-medium font-mono">{{ $payment->transaction_id }}</p>
            </div>
            @endif
            <div>
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('Data') }}</p>
                <p class="text-sm font-medium">{{ $payment->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-border dark:border-border-dark">
            <h3 class="text-sm font-semibold text-text-primary mb-3">{{ __('Atualizar Status') }}</h3>
            <form method="POST" action="{{ route('payments.update-status', $payment) }}" class="flex items-center gap-3">
                @csrf
                @method('PUT')
                <select name="status" class="input-field w-44">
                    <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>{{ __('Pendente') }}</option>
                    <option value="completed" {{ $payment->status === 'completed' ? 'selected' : '' }}>{{ __('Confirmado') }}</option>
                    <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>{{ __('Falha') }}</option>
                    <option value="refunded" {{ $payment->status === 'refunded' ? 'selected' : '' }}>{{ __('Reembolsado') }}</option>
                </select>
                <input type="text" name="transaction_id" placeholder="{{ __('ID da transação') }}" class="input-field flex-1" value="{{ $payment->transaction_id }}">
                <x-button variant="primary" size="sm">{{ __('Atualizar') }}</x-button>
            </form>
        </div>
    </x-card>
</div>

@endsection
