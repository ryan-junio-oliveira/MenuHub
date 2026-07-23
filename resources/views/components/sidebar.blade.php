@props(['active' => 'dashboard'])

@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
    $isActive = fn($pattern) => request()->routeIs($pattern);

    $isRoot = $user && $user->role === 'root';
    $isAdmin = $user && $user->role === 'admin';
    $isUser = $user && $user->role === 'user';

    if ($isRoot) {
        $navItems = [
            ['label' => __('Painel'), 'route' => 'root.dashboard', 'pattern' => 'root.dashboard', 'icon' => 'presentation-chart-bar'],
            ['label' => __('Restaurantes'), 'route' => 'root.restaurants.index', 'pattern' => 'root.restaurants.*', 'icon' => 'store'],
            ['label' => __('Usuários'), 'route' => 'root.users', 'pattern' => 'root.users', 'icon' => 'users'],
        ];
        $bottomIcon = 'user-circle';
        $bottomLabel = __('Perfil');
        $bottomRoute = 'profile.edit';
    } elseif ($isAdmin) {
        $navItems = [
            ['label' => __('Painel'), 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'presentation-chart-bar'],
            ['label' => __('Pedidos'), 'route' => 'orders.index', 'pattern' => 'orders.*', 'icon' => 'shopping-cart'],
            ['label' => __('Kanban da Cozinha'), 'route' => 'orders.kanban', 'pattern' => 'orders.kanban', 'icon' => 'rectangle-stack'],
            ['label' => __('Cardápio do Dia'), 'route' => 'daily-menus.index', 'pattern' => 'daily-menus.*', 'icon' => 'calendar-days'],
            ['label' => __('Pratos'), 'route' => 'dishes.index', 'pattern' => 'dishes.*', 'icon' => 'cake'],
            ['label' => __('Categorias'), 'route' => 'dish-categories.index', 'pattern' => 'dish-categories.*', 'icon' => 'tag'],
            ['label' => __('Clientes'), 'route' => 'customers.index', 'pattern' => 'customers.*', 'icon' => 'users'],
            ['label' => __('Relatórios'), 'route' => 'reports.index', 'pattern' => 'reports.*', 'icon' => 'chart-bar'],
            ['label' => __('Configurações'), 'route' => 'settings.index', 'pattern' => 'settings.*', 'icon' => 'cog-6-tooth'],
        ];
        $bottomIcon = 'user-circle';
        $bottomLabel = __('Perfil');
        $bottomRoute = 'profile.edit';
    } else {
        $navItems = [
            ['label' => __('Painel'), 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'presentation-chart-bar'],
            ['label' => __('Pedidos'), 'route' => 'orders.index', 'pattern' => 'orders.*', 'icon' => 'shopping-cart'],
            ['label' => __('Kanban da Cozinha'), 'route' => 'orders.kanban', 'pattern' => 'orders.kanban', 'icon' => 'rectangle-stack'],
        ];
        $bottomIcon = 'user-circle';
        $bottomLabel = __('Perfil');
        $bottomRoute = 'profile.edit';
    }
@endphp

@php
$icons = [
    'presentation-chart-bar' => '<i class="fa-solid fa-chart-pie fa-fw text-xl"></i>',
    'shopping-cart' => '<i class="fa-solid fa-cart-shopping fa-fw text-xl"></i>',
    'rectangle-stack' => '<i class="fa-solid fa-layer-group fa-fw text-xl"></i>',
    'calendar-days' => '<i class="fa-solid fa-calendar-days fa-fw text-xl"></i>',
    'cake' => '<i class="fa-solid fa-utensils fa-fw text-xl"></i>',
    'tag' => '<i class="fa-solid fa-tags fa-fw text-xl"></i>',
    'users' => '<i class="fa-solid fa-users fa-fw text-xl"></i>',
    'chart-bar' => '<i class="fa-solid fa-chart-simple fa-fw text-xl"></i>',
    'cog-6-tooth' => '<i class="fa-solid fa-gear fa-fw text-xl"></i>',
    'user-circle' => '<i class="fa-solid fa-user-circle fa-fw text-xl"></i>',
    'arrow-right-on-rectangle' => '<i class="fa-solid fa-right-from-bracket fa-fw text-xl"></i>',
    'store' => '<i class="fa-solid fa-store fa-fw text-xl"></i>',
];
@endphp

{{-- Desktop sidebar --}}
<div x-data :class="sidebarCollapsed ? 'w-20' : 'w-72'"
    class="hidden lg:flex fixed top-0 left-0 z-40 h-screen bg-slate-900 dark:bg-slate-950 transition-all duration-200 flex-col overflow-hidden shadow-xl">
    <div class="flex items-center justify-center h-navbar shrink-0 border-b border-slate-800 dark:border-slate-900">
        <div class="flex items-center gap-3 overflow-hidden px-4">
            <div x-show="!sidebarCollapsed" x-cloak>
                <x-logo dark class="h-10" />
            </div>
            <div x-show="sidebarCollapsed" x-cloak
                 class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-primary-600/20 shrink-0">
                M
            </div>
        </div>
    </div>

    <nav
        class="flex-1 py-4 space-y-1 scrollbar-thin"
        :class="sidebarCollapsed ? 'overflow-hidden px-1' : 'overflow-y-auto px-3'"
    >
        @foreach ($navItems as $item)
            @php
                $active = $isActive($item['pattern']);
            @endphp
            <a href="{{ route($item['route']) }}"
                class="sidebar-link group relative {{ $active ? 'sidebar-link-active bg-primary-600 text-white shadow-sm shadow-primary-600/10' : 'sidebar-link-inactive text-slate-400 hover:bg-slate-800 hover:text-white' }}"
                :class="sidebarCollapsed ? 'justify-center' : ''"
                title="{{ $item['label'] }}">
                {!! $icons[$item['icon']] !!}
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">{{ $item['label'] }}</span>
                <div x-cloak x-show="sidebarCollapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-slate-700 text-white text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50 shadow-lg">
                    {{ $item['label'] }}
                </div>
            </a>
        @endforeach
    </nav>

    <div class="shrink-0 border-t border-slate-800 dark:border-slate-900 py-4 px-3 space-y-1">
        <a href="{{ route($bottomRoute) }}"
            class="sidebar-link group relative {{ $isActive('profile.*') ? 'sidebar-link-active bg-primary-600 text-white shadow-sm' : 'sidebar-link-inactive text-slate-400 hover:bg-slate-800 hover:text-white' }}"
            :class="sidebarCollapsed ? 'justify-center' : ''"
            title="{{ $bottomLabel }}">
            {!! $icons[$bottomIcon] !!}
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">{{ $bottomLabel }}</span>
            <div x-cloak x-show="sidebarCollapsed"
                class="absolute left-full ml-2 px-2 py-1 bg-slate-700 text-white text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50 shadow-lg">
                {{ $bottomLabel }}
            </div>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="block">
            @csrf
            <button type="submit"
                class="sidebar-link group relative w-full text-slate-400 hover:bg-slate-800 hover:text-white"
                :class="sidebarCollapsed ? 'justify-center' : ''"
                title="{{ __('Sair') }}">
                {!! $icons['arrow-right-on-rectangle'] !!}
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">{{ __('Sair') }}</span>
                <div x-cloak x-show="sidebarCollapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-slate-700 text-white text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50 shadow-lg">
                    {{ __('Sair') }}
                </div>
            </button>
        </form>

        <button @click="sidebarCollapsed = !sidebarCollapsed"
            class="sidebar-link group relative w-full text-slate-400 hover:bg-slate-800 hover:text-white"
            :class="sidebarCollapsed ? 'justify-center' : ''"
            :title="sidebarCollapsed ? 'Expandir' : 'Recolher'">
            <i x-show="!sidebarCollapsed" class="fa-solid fa-chevron-left fa-fw text-xl"></i>
            <i x-show="sidebarCollapsed" x-cloak class="fa-solid fa-chevron-right fa-fw text-xl"></i>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap"
                x-text="sidebarCollapsed ? 'Expandir' : 'Recolher'"></span>
            <div x-cloak x-show="sidebarCollapsed"
                class="absolute left-full ml-2 px-2 py-1 bg-slate-700 text-white text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50 shadow-lg">
                <span x-text="sidebarCollapsed ? 'Expandir' : 'Recolher'"></span>
            </div>
        </button>
    </div>
</div>

{{-- Mobile sidebar overlay --}}
<div x-cloak x-show="mobileSidebarOpen" class="fixed inset-0 z-50 lg:hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="mobileSidebarOpen = false"></div>
    <div
        class="absolute top-0 left-0 h-full w-72 bg-slate-900 dark:bg-slate-950 shadow-xl flex flex-col overflow-hidden">
        <div
            class="flex items-center justify-between h-navbar shrink-0 border-b border-slate-800 dark:border-slate-900 px-4">
            <div class="flex items-center gap-3">
                <x-logo dark variant="sm" />
            </div>
            <button @click="mobileSidebarOpen = false" class="text-slate-400 hover:text-white p-1">
                <i class="fa-solid fa-xmark w-6 h-6"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 space-y-1" :class="sidebarCollapsed ? 'px-2' : 'px-3'">
            @foreach ($navItems as $item)
                @php $active = $isActive($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}" @click="mobileSidebarOpen = false"
                    class="sidebar-link {{ $active ? 'sidebar-link-active bg-primary-600 text-white shadow-sm' : 'sidebar-link-inactive text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    {!! $icons[$item['icon']] !!}
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="shrink-0 border-t border-slate-800 dark:border-slate-900 py-4 px-3 space-y-1">
            <a href="{{ route($bottomRoute) }}" @click="mobileSidebarOpen = false"
                class="sidebar-link {{ $isActive('profile.*') ? 'sidebar-link-active bg-primary-600 text-white shadow-sm' : 'sidebar-link-inactive text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                {!! $icons[$bottomIcon] !!}
                <span>{{ $bottomLabel }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit"
                    class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    {!! $icons['arrow-right-on-rectangle'] !!}
                    <span>{{ __('Sair') }}</span>
                </button>
            </form>
        </div>
    </div>
</div>
