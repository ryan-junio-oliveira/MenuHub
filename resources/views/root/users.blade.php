@extends('layouts.app')
@section('title', 'Usuários')
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">Usuários</h1>
            <p class="page-subtitle">Gerenciar usuários do sistema</p>
        </div>
        <a href="{{ route('root.users.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus text-sm"></i>
            Novo Usuário
        </a>
    </div>

    @if (session('success'))
    <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
        <i class="fa-regular fa-circle-check mr-1.5"></i> {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
        <i class="fa-regular fa-circle-xmark mr-1.5"></i> {{ session('error') }}
    </div>
    @endif

    <x-card padding="0">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">Nome</th>
                        <th class="table-th">E-mail</th>
                        <th class="table-th">Função</th>
                        <th class="table-th">Restaurante</th>
                        <th class="table-th">Cadastro</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @forelse ($users as $user)
                    <tr class="table-row">
                        <td class="table-td font-medium">{{ $user->name }}</td>
                        <td class="table-td">{{ $user->email }}</td>
                        <td class="table-td">
                            @if ($user->role === 'admin')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                                Administrador
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                Usuário
                            </span>
                            @endif
                        </td>
                        <td class="table-td">{{ $user->restaurant?->name ?? '-' }}</td>
                        <td class="table-td text-sm text-text-secondary">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="table-td text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('root.users.edit', $user) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('root.users.destroy', $user) }}" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-600 dark:text-red-400">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="table-td text-center py-12 text-text-secondary">
                            <i class="fa-solid fa-users text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                            Nenhum usuário cadastrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

@endsection
