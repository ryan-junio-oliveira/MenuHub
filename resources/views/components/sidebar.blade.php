@props(['active' => 'dashboard'])

@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
    $isActive = fn($pattern) => request()->routeIs($pattern);

    $isRoot = $user && $user->role === 'root';
    $isAdmin = $user && $user->role === 'admin';

    $plan = $user?->restaurant?->plan;

    if ($isRoot) {
        $navItems = [
            ['label' => __('Painel'), 'route' => 'root.dashboard', 'pattern' => 'root.dashboard', 'icon' => 'presentation-chart-bar'],
            ['label' => __('Restaurantes'), 'route' => 'root.restaurants.index', 'pattern' => 'root.restaurants.*', 'icon' => 'store'],
            ['label' => __('Usuários'), 'route' => 'root.users', 'pattern' => 'root.users', 'icon' => 'users'],
            ['label' => __('Pedidos Globais'), 'route' => 'root.orders', 'pattern' => 'root.orders', 'icon' => 'shopping-cart'],
            ['label' => __('Cobranças'), 'route' => 'root.billing.index', 'pattern' => 'root.billing.*', 'icon' => 'credit-card'],
        ];
        $menuGroups = [];
        $bottomIcon = 'user-circle';
        $bottomLabel = __('Perfil');
        $bottomRoute = 'profile.edit';
    } elseif ($isAdmin) {
        $navItems = [
            ['label' => __('Painel'), 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'presentation-chart-bar'],
        ];

        $menuGroups = [
            'Pedidos' => [
                'icon' => 'shopping-cart',
                'items' => [
                    ['label' => __('Pedidos'), 'route' => 'orders.index', 'pattern' => 'orders.index,orders.show,orders.edit,orders.create'],
                    ['label' => __('Kanban'), 'route' => 'orders.kanban', 'pattern' => 'orders.kanban'],
                ],
            ],
            'Cardapio' => [
                'icon' => 'calendar-days',
                'items' => [
                    ['label' => __('Cardapio do Dia'), 'route' => 'daily-menus.index', 'pattern' => 'daily-menus.*'],
                    ['label' => __('Pratos'), 'route' => 'dishes.index', 'pattern' => 'dishes.*'],
                    ['label' => __('Categorias'), 'route' => 'dish-categories.index', 'pattern' => 'dish-categories.*'],
                    ['label' => __('Opcoes'), 'route' => 'menu-options.index', 'pattern' => 'menu-options.*'],
                ],
            ],
        ];

        $extraItems = [
            ['label' => __('Clientes'), 'route' => 'customers.index', 'pattern' => 'customers.*', 'icon' => 'users'],
            ['label' => __('Relatorios'), 'route' => 'reports.index', 'pattern' => 'reports.*', 'icon' => 'chart-bar', 'feature' => 'reports'],
            ['label' => __('Entregas'), 'route' => 'deliveries.index', 'pattern' => 'deliveries.*', 'icon' => 'truck', 'feature' => 'delivery_management'],
            ['label' => __('Pagamentos'), 'route' => 'payments.index', 'pattern' => 'payments.*', 'icon' => 'credit-card'],
            ['label' => __('Configuracoes'), 'route' => 'settings.index', 'pattern' => 'settings.*', 'icon' => 'cog-6-tooth'],
        ];

        $extraItems = array_values(array_filter($extraItems, fn($item) => !isset($item['feature']) || ($plan && $plan->hasFeature($item['feature']))));

        $bottomIcon = 'user-circle';
        $bottomLabel = __('Perfil');
        $bottomRoute = 'profile.edit';
    } else {
        $navItems = [
            ['label' => __('Painel'), 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'presentation-chart-bar'],
        ];
        $menuGroups = [
            'Pedidos' => [
                'icon' => 'shopping-cart',
                'items' => [
                    ['label' => __('Pedidos'), 'route' => 'orders.index', 'pattern' => 'orders.index,orders.show,orders.edit,orders.create'],
                    ['label' => __('Kanban'), 'route' => 'orders.kanban', 'pattern' => 'orders.kanban'],
                ],
            ],
        ];
        $extraItems = [];
        $bottomIcon = 'user-circle';
        $bottomLabel = __('Perfil');
        $bottomRoute = 'profile.edit';
    }

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
        'credit-card' => '<i class="fa-solid fa-credit-card fa-fw text-xl"></i>',
        'truck' => '<i class="fa-solid fa-truck fa-fw text-xl"></i>',
        'list-bullet' => '<i class="fa-solid fa-list fa-fw text-xl"></i>',
    ];

    $routeToGroup = [];
    foreach ($menuGroups as $gKey => $group) {
        foreach ($group['items'] as $item) {
            foreach (explode(',', $item['pattern']) as $pat) {
                $routeToGroup[trim($pat)] = $gKey;
            }
        }
    }

    $groupIsActive = fn($items) => collect($items)->contains(fn($i) => collect(explode(',', $i['pattern']))->contains(fn($p) => request()->routeIs(trim($p))));
@endphp

{{-- Desktop sidebar --}}
<div
    x-data="{
        openGroups: JSON.parse(localStorage.getItem('sidebar-groups') || '{}'),
        toggleGroup(key) {
            this.openGroups[key] = !this.openGroups[key];
            localStorage.setItem('sidebar-groups', JSON.stringify(this.openGroups));
        }
    }"
    :class="sidebarCollapsed ? 'w-20' : 'w-72'"
    class="hidden lg:flex fixed top-0 left-0 z-40 h-screen bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-all duration-200 flex-col overflow-hidden shadow-lg dark:shadow-none"
>
    {{-- Logo --}}
    <div class="flex items-center justify-center h-navbar shrink-0 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3 overflow-hidden px-4">
            <div x-show="!sidebarCollapsed" x-cloak>
                <img src="{{ asset('assets/img/logo_full_removebg.png') }}" alt="MenuHub" class="h-10 w-auto block dark:hidden">
                <img src="{{ asset('assets/img/logo_full_dark_removebg.png') }}" alt="MenuHub" class="h-10 w-auto hidden dark:block">
            </div>
            <div x-show="sidebarCollapsed" x-cloak
                 class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-primary-600/20 shrink-0">
                M
            </div>
        </div>
    </div>

    <nav
        class="flex-1 py-4 space-y-0.5 scrollbar-thin"
        :class="sidebarCollapsed ? 'overflow-hidden px-1' : 'overflow-y-auto px-3'"
    >
        {{-- Top-level links --}}
        @foreach ($navItems as $item)
            @php
                $isItemActive = $isActive($item['pattern']);
            @endphp
            <a href="{{ route($item['route']) }}"
                class="sidebar-link group relative {{ $isItemActive ? 'sidebar-link-active' : 'sidebar-link-inactive' }}"
                :class="sidebarCollapsed ? 'justify-center' : ''"
                title="{{ $item['label'] }}">
                {!! $icons[$item['icon']] !!}
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">{{ $item['label'] }}</span>
                <div x-cloak x-show="sidebarCollapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50 shadow-lg">
                    {{ $item['label'] }}
                </div>
            </a>
        @endforeach

        {{-- Groups with sub-items --}}
        @foreach ($menuGroups as $groupName => $group)
            @php
                $gKey = 'sidebar-group-' . \Illuminate\Support\Str::slug($groupName);
                $isGActive = $groupIsActive($group['items']);
                $isOpen = false;
                foreach ($group['items'] as $gi) {
                    foreach (explode(',', $gi['pattern']) as $pat) {
                        if (request()->routeIs(trim($pat))) { $isOpen = true; break 2; }
                    }
                }
            @endphp
            <div>
                <button type="button"
                    x-on:click="toggleGroup('{{ $gKey }}')"
                    :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                    class="sidebar-group-header w-full {{ $isGActive ? 'sidebar-group-header-active' : 'sidebar-group-header-inactive' }}"
                    :title="sidebarCollapsed ? '{{ $groupName }}' : ''">
                    <span x-show="!sidebarCollapsed" class="flex items-center gap-3">
                        {!! $icons[$group['icon']] !!}
                        <span>{{ $groupName }}</span>
                    </span>
                    <span x-show="!sidebarCollapsed">
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': openGroups['{{ $gKey }}'] }"></i>
                    </span>
                </button>

                <div x-show="openGroups['{{ $gKey }}'] || {{ $isOpen ? 'true' : 'false' }}"
                    x-cloak
                    class="space-y-0.5 mt-0.5 {{ $isOpen ? '' : '' }}">
                    @foreach ($group['items'] as $gi)
                        @php
                            $giActive = collect(explode(',', $gi['pattern']))->contains(fn($p) => request()->routeIs(trim($p)));
                        @endphp
                        <a href="{{ route($gi['route']) }}"
                            class="sidebar-sub-link group relative {{ $giActive ? 'sidebar-sub-active' : 'sidebar-sub-inactive' }}"
                            :class="sidebarCollapsed ? 'justify-center' : 'pl-11'"
                            title="{{ $gi['label'] }}">
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">{{ $gi['label'] }}</span>
                            <div x-cloak x-show="sidebarCollapsed"
                                class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50 shadow-lg">
                                {{ $gi['label'] }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Extra standalone items --}}
        @foreach ($extraItems ?? [] as $item)
            @php
                $isItemActive = $isActive($item['pattern']);
            @endphp
            <a href="{{ route($item['route']) }}"
                class="sidebar-link group relative {{ $isItemActive ? 'sidebar-link-active' : 'sidebar-link-inactive' }}"
                :class="sidebarCollapsed ? 'justify-center' : ''"
                title="{{ $item['label'] }}">
                {!! $icons[$item['icon']] !!}
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">{{ $item['label'] }}</span>
                <div x-cloak x-show="sidebarCollapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50 shadow-lg">
                    {{ $item['label'] }}
                </div>
            </a>
        @endforeach
    </nav>

    {{-- Bottom section --}}
    <div class="shrink-0 border-t border-slate-200 dark:border-slate-800 py-4 px-3 space-y-1">
        <a href="{{ route($bottomRoute) }}"
            class="sidebar-link group relative {{ $isActive('profile.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}"
            :class="sidebarCollapsed ? 'justify-center' : ''"
            title="{{ $bottomLabel }}">
            {!! $icons[$bottomIcon] !!}
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">{{ $bottomLabel }}</span>
            <div x-cloak x-show="sidebarCollapsed"
                class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50 shadow-lg">
                {{ $bottomLabel }}
            </div>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="block">
            @csrf
            <button type="submit"
                class="sidebar-link group relative w-full sidebar-link-inactive"
                :class="sidebarCollapsed ? 'justify-center' : ''"
                title="{{ __('Sair') }}">
                {!! $icons['arrow-right-on-rectangle'] !!}
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">{{ __('Sair') }}</span>
                <div x-cloak x-show="sidebarCollapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50 shadow-lg">
                    {{ __('Sair') }}
                </div>
            </button>
        </form>

        <button @click="sidebarCollapsed = !sidebarCollapsed"
            class="sidebar-link group relative w-full sidebar-link-inactive"
            :class="sidebarCollapsed ? 'justify-center' : ''"
            :title="sidebarCollapsed ? 'Expandir' : 'Recolher'">
            <i x-show="!sidebarCollapsed" class="fa-solid fa-chevron-left fa-fw text-xl"></i>
            <i x-show="sidebarCollapsed" x-cloak class="fa-solid fa-chevron-right fa-fw text-xl"></i>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap"
                x-text="sidebarCollapsed ? 'Expandir' : 'Recolher'"></span>
            <div x-cloak x-show="sidebarCollapsed"
                class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50 shadow-lg">
                <span x-text="sidebarCollapsed ? 'Expandir' : 'Recolher'"></span>
            </div>
        </button>
    </div>
</div>

{{-- Mobile sidebar overlay --}}
<div x-cloak x-show="mobileSidebarOpen" class="fixed inset-0 z-50 lg:hidden"
    x-data="{
        openGroups: JSON.parse(localStorage.getItem('sidebar-groups') || '{}'),
        toggleGroup(key) {
            this.openGroups[key] = !this.openGroups[key];
            localStorage.setItem('sidebar-groups', JSON.stringify(this.openGroups));
        }
    }">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="mobileSidebarOpen = false"></div>
    <div
        class="absolute top-0 left-0 h-full w-72 bg-white dark:bg-slate-900 shadow-xl flex flex-col overflow-hidden">
        <div
            class="flex items-center justify-between h-navbar shrink-0 border-b border-slate-200 dark:border-slate-800 px-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/img/logo_full_removebg.png') }}" alt="MenuHub" class="h-10 w-auto block dark:hidden">
                <img src="{{ asset('assets/img/logo_full_dark_removebg.png') }}" alt="MenuHub" class="h-10 w-auto hidden dark:block">
            </div>
            <button @click="mobileSidebarOpen = false" class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white p-1">
                <i class="fa-solid fa-xmark w-6 h-6"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 space-y-0.5 px-3">
            @foreach ($navItems as $item)
                @php $a = $isActive($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}" @click="mobileSidebarOpen = false"
                    class="sidebar-link {{ $a ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                    {!! $icons[$item['icon']] !!}
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

            @foreach ($menuGroups as $groupName => $group)
                @php
                    $gKey = 'sidebar-group-' . \Illuminate\Support\Str::slug($groupName);
                    $isGActive = $groupIsActive($group['items']);
                    $isOpen = false;
                    foreach ($group['items'] as $gi) {
                        foreach (explode(',', $gi['pattern']) as $pat) {
                            if (request()->routeIs(trim($pat))) { $isOpen = true; break 2; }
                        }
                    }
                @endphp
                <div>
                    <button type="button"
                        x-on:click="toggleGroup('{{ $gKey }}')"
                        class="sidebar-group-header w-full {{ $isGActive ? 'sidebar-group-header-active' : 'sidebar-group-header-inactive' }}">
                        <span class="flex items-center gap-3">
                            {!! $icons[$group['icon']] !!}
                            <span>{{ $groupName }}</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': openGroups['{{ $gKey }}'] }"></i>
                    </button>
                    <div x-show="openGroups['{{ $gKey }}'] || {{ $isOpen ? 'true' : 'false' }}"
                        x-cloak class="space-y-0.5 mt-0.5 ml-2">
                        @foreach ($group['items'] as $gi)
                            @php $giActive = collect(explode(',', $gi['pattern']))->contains(fn($p) => request()->routeIs(trim($p))); @endphp
                            <a href="{{ route($gi['route']) }}" @click="mobileSidebarOpen = false"
                                class="sidebar-sub-link pl-11 {{ $giActive ? 'sidebar-sub-active' : 'sidebar-sub-inactive' }}">
                                <span>{{ $gi['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @foreach ($extraItems ?? [] as $item)
                @php $a = $isActive($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}" @click="mobileSidebarOpen = false"
                    class="sidebar-link {{ $a ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                    {!! $icons[$item['icon']] !!}
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="shrink-0 border-t border-slate-200 dark:border-slate-800 py-4 px-3 space-y-1">
            <a href="{{ route($bottomRoute) }}" @click="mobileSidebarOpen = false"
                class="sidebar-link {{ $isActive('profile.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                {!! $icons[$bottomIcon] !!}
                <span>{{ $bottomLabel }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit"
                    class="sidebar-link w-full sidebar-link-inactive">
                    {!! $icons['arrow-right-on-rectangle'] !!}
                    <span>{{ __('Sair') }}</span>
                </button>
            </form>
        </div>
    </div>
</div>
