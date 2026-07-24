@extends('layouts.app')
@section('title', 'Restaurantes')
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">Restaurantes</h1>
        <p class="page-subtitle">Gerenciar restaurantes cadastrados</p>
    </div>

    <x-card padding="0">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">Nome</th>
                        <th class="table-th">E-mail</th>
                        <th class="table-th">Telefone</th>
                        <th class="table-th">Usuários</th>
                        <th class="table-th">Cadastro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @forelse ($restaurants as $restaurant)
                    <tr class="table-row">
                        <td class="table-td font-medium">{{ $restaurant->name }}</td>
                        <td class="table-td">{{ $restaurant->email ?? '-' }}</td>
                        <td class="table-td">{{ $restaurant->phone ?? '-' }}</td>
                        <td class="table-td">{{ $restaurant->users_count }}</td>
                        <td class="table-td text-sm text-text-secondary">{{ $restaurant->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="table-td text-center py-12 text-text-secondary">
                            <i class="fa-solid fa-store text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                            Nenhum restaurante cadastrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

@endsection
