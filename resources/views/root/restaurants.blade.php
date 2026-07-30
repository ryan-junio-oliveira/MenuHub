@extends('layouts.app')
@section('title', 'Restaurantes')
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1 class="page-title">Restaurantes</h1>
            <p class="page-subtitle">Gerenciar restaurantes cadastrados no sistema</p>
        </div>
        <a href="{{ route('root.restaurants.create') }}" class="btn-primary text-sm">
            <i class="fa-solid fa-plus text-xs"></i>
            Novo Restaurante
        </a>
    </div>

    @php
        $statusConfig = [
            'active' => ['label' => 'Ativos', 'color' => 'text-green-600', 'bg' => 'bg-green-100 dark:bg-green-900/30'],
            'inactive' => ['label' => 'Inativos', 'color' => 'text-red-600', 'bg' => 'bg-red-100 dark:bg-red-900/30'],
        ];
        $activeCount = $restaurants->where('is_active', true)->count();
        $inactiveCount = $restaurants->where('is_active', false)->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <p class="stat-label">Total</p>
            <p class="stat-value">{{ $restaurants->count() }}</p>
        </div>
        <div class="stat-card border-green-200 dark:border-green-800">
            <p class="stat-label text-green-600 dark:text-green-400">Ativos</p>
            <p class="stat-value text-green-600">{{ $activeCount }}</p>
        </div>
        <div class="stat-card border-red-200 dark:border-red-800">
            <p class="stat-label text-red-600 dark:text-red-400">Inativos</p>
            <p class="stat-value text-red-600">{{ $inactiveCount }}</p>
        </div>
    </div>

    <x-card padding="0">
        <div class="p-4 border-b border-border dark:border-border-dark bg-surface dark:bg-surface-dark">
            <div class="flex items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-text-secondary"></i>
                    <input type="text" id="restaurantSearch" placeholder="Buscar restaurante..." class="input-field pl-9" oninput="filterRestaurants(this.value)">
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="restaurantsTable">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">Restaurante</th>
                        <th class="table-th">Contato</th>
                        <th class="table-th">Usuários</th>
                        <th class="table-th">Pedidos</th>
                        <th class="table-th">Status</th>
                        <th class="table-th">Convite</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-border-dark">
                    @forelse ($restaurants as $restaurant)
                    <tr class="table-row search-row">
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                    @if ($restaurant->logo)
                                    <img src="{{ Storage::url($restaurant->logo) }}" alt="" class="w-9 h-9 rounded-lg object-cover">
                                    @else
                                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ substr($restaurant->name, 0, 2) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-text-primary search-name">{{ $restaurant->name }}</p>
                                    <p class="text-xs text-text-secondary">{{ $restaurant->email ?? 'Sem email' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="table-td text-sm text-text-secondary">{{ $restaurant->phone ?? '-' }}</td>
                        <td class="table-td text-sm font-medium">{{ $restaurant->users_count }}</td>
                        <td class="table-td text-sm font-medium">{{ $restaurant->orders_count ?? 0 }}</td>
                        <td class="table-td">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $restaurant->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $restaurant->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $restaurant->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="table-td">
                            @if ($restaurant->setup_completed_at)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                    <i class="fa-regular fa-circle-check text-xs"></i> Completo
                                </span>
                            @elseif ($restaurant->invitation_failed_at)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300" title="Falhou em {{ $restaurant->invitation_failed_at->format('d/m/Y H:i') }}">
                                    <i class="fa-regular fa-circle-xmark text-xs"></i> Falha
                                </span>
                            @elseif ($restaurant->invitation_sent_at)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300" title="Enviado em {{ $restaurant->invitation_sent_at->format('d/m/Y H:i') }}">
                                    <i class="fa-regular fa-clock text-xs"></i> Pendente
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                    <i class="fa-regular fa-hourglass text-xs"></i> Aguardando
                                </span>
                            @endif
                        </td>
                        <td class="table-td text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('root.restaurants.show', $restaurant) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" title="Detalhes">
                                    <i class="fa-regular fa-eye text-sm"></i>
                                </a>
                                <form method="POST" action="{{ route('root.restaurants.toggle-active', $restaurant) }}" class="inline">
                                    @csrf @method('PUT')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-{{ $restaurant->is_active ? 'red' : 'green' }}-600 hover:bg-{{ $restaurant->is_active ? 'red' : 'green' }}-50 transition-colors" title="{{ $restaurant->is_active ? 'Desativar' : 'Ativar' }}">
                                        <i class="fa-solid fa-{{ $restaurant->is_active ? 'pause' : 'play' }} text-sm"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('root.restaurants.destroy', $restaurant) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este restaurante? Todas as operacoes associadas serao removidas.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-text-secondary hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Excluir">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="table-td text-center py-12 text-text-secondary">
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

@push('scripts')
<script>
    function filterRestaurants(query) {
        const rows = document.querySelectorAll('.search-row');
        rows.forEach(row => {
            const name = row.querySelector('.search-name')?.textContent?.toLowerCase() || '';
            row.style.display = name.includes(query.toLowerCase()) ? '' : 'none';
        });
    }
</script>
@endpush

@endsection
