@extends('layouts.app')
@section('title', $customerTag->name)
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('customer-tags.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i> {{ __('Voltar às Tags') }}
        </a>
    </div>

    <div class="page-header flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="w-4 h-4 rounded-full" style="background-color: {{ $customerTag->color }}"></span>
            <div>
                <h1 class="page-title">{{ $customerTag->name }}</h1>
                <p class="page-subtitle">{{ $customers->count() }} {{ __('clientes com esta tag') }}</p>
            </div>
        </div>
        <a href="{{ route('customer-tags.edit', $customerTag) }}" class="btn-secondary text-sm">
            <i class="fa-regular fa-pen-to-square text-sm mr-1"></i> {{ __('Editar') }}
        </a>
    </div>

    <x-card padding="0">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">{{ __('Cliente') }}</th>
                        <th class="table-th">{{ __('Telefone') }}</th>
                        <th class="table-th">{{ __('Pedidos') }}</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @forelse ($customers as $customer)
                    <tr class="table-row">
                        <td class="table-td font-medium">{{ $customer->name }}</td>
                        <td class="table-td text-text-secondary">{{ $customer->phone ?? '-' }}</td>
                        <td class="table-td text-text-secondary">{{ $customer->orders_count }}</td>
                        <td class="table-td text-right">
                            <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-primary-600 transition-colors" title="Visualizar">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="table-td text-center py-12 text-text-secondary">
                            <i class="fa-solid fa-users text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                            {{ __('Nenhum cliente com esta tag.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

@endsection
