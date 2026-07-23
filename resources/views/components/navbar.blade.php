@props([
    'restaurantName' => 'MenuHub',
    'userName' => '',
    'userEmail' => '',
])

@php
    $userName = $userName ?: (Auth::user()->name ?? '');
    $userEmail = $userEmail ?: (Auth::user()->email ?? '');
@endphp

<header
    x-data="{ profileOpen: false, notificationsOpen: false }"
    class="sticky top-0 z-30 h-navbar border-b border-border dark:border-border-dark bg-card dark:bg-card-dark flex items-center justify-between px-4 lg:px-6"
>
    <div class="flex items-center gap-3">
        <button
            @click="mobileSidebarOpen = !mobileSidebarOpen"
            class="lg:hidden inline-flex items-center justify-center p-2 -ml-2 rounded-lg text-text-secondary hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-150"
            aria-label="{{ __('Alternar sidebar') }}"
        >
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </div>

    <div class="flex items-center gap-1">
        <button
            @click="
                darkMode = !darkMode;
                document.documentElement.classList.toggle('dark', darkMode);
                localStorage.setItem('darkMode', darkMode);
            "
            class="relative p-2 rounded-lg text-text-secondary hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-150"
            :title="darkMode ? 'Modo Claro' : 'Modo Escuro'"
        >
            <i x-show="!darkMode" class="fa-solid fa-moon text-lg"></i>
            <i x-show="darkMode" x-cloak class="fa-solid fa-sun text-lg"></i>
        </button>

        <div x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2 rounded-lg text-text-secondary hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-150" aria-label="{{ __('Notificações') }}">
                <i class="fa-regular fa-bell text-lg"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-card dark:ring-card-dark"></span>
            </button>
            <div x-show="open" x-cloak
                class="fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24"
                @click.self="open = false"
            >
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="open = false"></div>
                <div
                    class="relative w-[90%] md:w-[70%] lg:w-[50%] max-w-2xl bg-card dark:bg-card-dark rounded-2xl shadow-2xl overflow-hidden"
                    x-trap.noscroll="open"
                >
                    <div class="flex items-center justify-between px-6 py-4 bg-primary-600 text-white">
                        <div class="flex items-center gap-3">
                            <i class="fa-regular fa-bell text-xl"></i>
                            <h2 class="text-lg font-semibold">{{ __('Notificações') }}</h2>
                        </div>
                        <button @click="open = false" class="text-white/80 hover:text-white transition-colors">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <div class="p-12 text-center">
                        <i class="fa-regular fa-bell text-5xl text-slate-300 dark:text-slate-600 mb-4 block"></i>
                        <p class="text-sm text-text-secondary">{{ __('Nenhuma notificação') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 px-2.5 py-2 rounded-lg text-sm font-medium text-text-primary dark:text-text-dark hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-150">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                    {{ substr($userName, 0, 1) }}
                </div>
                <span class="hidden sm:block max-w-[120px] truncate">{{ $userName }}</span>
                <i class="hidden sm:block fa-solid fa-chevron-down text-xs text-text-secondary"></i>
            </button>
            <div x-show="open" x-cloak @click.outside="open = false" class="absolute right-0 z-50 mt-2 w-56 rounded-xl border border-border dark:border-border-dark bg-card dark:bg-card-dark shadow-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-border dark:border-border-dark">
                    <p class="text-sm font-medium text-text-primary dark:text-text-dark truncate">{{ $userName }}</p>
                    <p class="text-xs text-text-secondary truncate">{{ $userEmail }}</p>
                </div>
                <div class="p-1.5">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm text-text-primary dark:text-text-dark hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <i class="fa-regular fa-user text-sm text-text-secondary w-4"></i>
                        {{ __('Perfil') }}
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm text-text-primary dark:text-text-dark hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <i class="fa-solid fa-gear text-sm text-text-secondary w-4"></i>
                        {{ __('Configurações') }}
                    </a>
                    <hr class="border-border dark:border-border-dark my-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                            {{ __('Sair') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
