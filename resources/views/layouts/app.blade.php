<!DOCTYPE html>
<html lang="pt-BR" class="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title') @yield('title') - @endif{{ __('MenuHub') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased" x-data="{
        sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true',
        mobileSidebarOpen: false,
        darkMode: localStorage.getItem('darkMode') === 'true'
    }" x-init="
        if (darkMode) document.documentElement.classList.add('dark');
        $watch('darkMode', val => {
            document.documentElement.classList.toggle('dark', val);
            localStorage.setItem('darkMode', val);
        });
        $watch('sidebarCollapsed', val => localStorage.setItem('sidebar-collapsed', val));
    "
    @toggle-sidebar.window="mobileSidebarOpen = !mobileSidebarOpen"
    @close-mobile-sidebar.window="mobileSidebarOpen = false"
    >
        <div class="min-h-screen bg-surface dark:bg-surface-dark">

            <x-sidebar />

            <div
                class="transition-all duration-200"
                :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-72'"
            >
                <x-navbar
                    restaurantName="{{ auth()->user()->restaurant?->name ?? __('MenuHub') }}"
                    userName="{{ auth()->user()->name }}"
                    userEmail="{{ auth()->user()->email }}"
                />

                <main class="p-6 lg:p-8">
                    @if(session('success'))
                        <x-alert type="success" :message="session('success')" dismissible class="mb-6" />
                    @endif
                    @if(session('error'))
                        <x-alert type="error" :message="session('error')" dismissible class="mb-6" />
                    @endif
                    @if(session('warning'))
                        <x-alert type="warning" :message="session('warning')" dismissible class="mb-6" />
                    @endif
                    @if(session('info'))
                        <x-alert type="info" :message="session('info')" dismissible class="mb-6" />
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
