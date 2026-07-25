<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('MenuHub') }} — {{ __('Gestão Inteligente de Restaurantes') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/landing-animations.js'])
</head>
<body class="font-sans antialiased bg-slate-950 text-white overflow-x-hidden">

    <div x-data="{ scrolled: false, mobileMenu: false }" x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 50)" class="relative">

        {{-- NAVBAR --}}
        <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
                :class="scrolled ? 'bg-slate-950/90 backdrop-blur-xl border-b border-slate-800/50 shadow-lg shadow-black/20' : 'bg-transparent'">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-18 py-4">
                    <a href="/" class="flex items-center gap-2.5 group">
                        <x-logo dark class="h-9 group-hover:scale-105 transition-transform" />
                    </a>

                    <nav class="hidden md:flex items-center gap-8">
                        <a href="#features" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">{{ __('Recursos') }}</a>
                        <a href="#stats" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">{{ __('Números') }}</a>
                        <a href="#cta" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">{{ __('Contato') }}</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 px-5 py-2.5 rounded-xl transition-all hover:shadow-lg hover:shadow-primary-600/30">
                                    <i class="fa-solid fa-gauge-high"></i>
                                    {{ __('Painel') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 px-5 py-2.5 rounded-xl transition-all hover:shadow-lg hover:shadow-primary-600/30">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                    {{ __('Acessar') }}
                                </a>
                            @endauth
                        @endif
                    </nav>

                    <button @click="mobileMenu = !mobileMenu" class="md:hidden text-slate-400 hover:text-white p-2">
                        <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div x-show="mobileMenu" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="md:hidden border-t border-slate-800 bg-slate-950/95 backdrop-blur-xl">
                <div class="px-4 py-6 space-y-4">
                    <a href="#features" @click="mobileMenu = false" class="block text-sm font-medium text-slate-400 hover:text-white">{{ __('Recursos') }}</a>
                    <a href="#stats" @click="mobileMenu = false" class="block text-sm font-medium text-slate-400 hover:text-white">{{ __('Números') }}</a>
                    <a href="#cta" @click="mobileMenu = false" class="block text-sm font-medium text-slate-400 hover:text-white">{{ __('Contato') }}</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block w-full text-center text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 px-5 py-3 rounded-xl">{{ __('Painel') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 px-5 py-3 rounded-xl">{{ __('Acessar') }}</a>
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        {{-- HERO --}}
        <section class="relative min-h-screen flex items-center overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-primary-950/50 via-slate-950 to-slate-950"></div>

            <div class="absolute inset-0 opacity-20"
                 style="background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px); background-size: 60px 60px;">
            </div>

            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary-500/20 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-primary-400/10 rounded-full blur-[100px] animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute top-1/3 right-1/3 w-64 h-64 bg-primary-600/10 rounded-full blur-[80px] animate-pulse" style="animation-delay: 2s"></div>

            <div class="absolute top-20 left-10 w-16 h-16 border border-primary-500/30 rounded-xl rotate-45 animate-float opacity-30 hidden lg:block"></div>
            <div class="absolute bottom-40 right-20 w-20 h-20 border border-primary-400/20 rounded-full animate-float opacity-20 hidden lg:block" style="animation-delay: 1.5s"></div>
            <div class="absolute top-1/2 right-10 w-12 h-12 bg-primary-500/10 rounded-lg rotate-12 animate-float opacity-25 hidden lg:block" style="animation-delay: 0.8s"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20">
                <div class="text-center max-w-4xl mx-auto hero-content">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-500/10 border border-primary-500/20 rounded-full text-sm text-primary-300 mb-8">
                        <span class="w-2 h-2 bg-primary-400 rounded-full animate-pulse"></span>
                        {{ __('Gestão inteligente para restaurantes') }}
                    </div>

                    <h1 class="text-5xl sm:text-6xl lg:text-8xl font-black tracking-tight leading-none animate-fade-in-up animation-delay-200">
                        <span class="bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">{{ __('Cardápio, Pedidos') }}</span>
                        <br>
                        <span class="bg-gradient-to-r from-primary-400 via-primary-500 to-primary-600 bg-clip-text text-transparent">{{ __('e Gestão') }}</span>
                    </h1>

                    <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed animate-fade-in-up animation-delay-400">
                        {{ __('Centralize seus cardápios, receba pedidos em tempo real, gerencie entregas e acompanhe relatórios — tudo em um só lugar.') }}
                    </p>

                    <div class="mt-10 flex items-center justify-center gap-4 flex-wrap animate-fade-in-up animation-delay-600">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2.5 px-7 py-3.5 bg-primary-600 text-white font-bold text-base rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-600/30 hover:shadow-2xl hover:shadow-primary-600/40 hover:scale-[1.02] active:scale-[0.98]">
                                    <i class="fa-solid fa-gauge-high"></i>
                                    {{ __('Ir para o Painel') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2.5 px-7 py-3.5 bg-primary-600 text-white font-bold text-base rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-600/30 hover:shadow-2xl hover:shadow-primary-600/40 hover:scale-[1.02] active:scale-[0.98]">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                    {{ __('Acessar o Sistema') }}
                                </a>
                            @endauth
                        @endif
                        <a href="#features" class="inline-flex items-center gap-2.5 px-7 py-3.5 border border-slate-700 text-slate-300 font-semibold text-base rounded-2xl hover:border-primary-500/50 hover:text-white transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <i class="fa-solid fa-chevron-down"></i>
                            {{ __('Saber Mais') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-slate-950 to-transparent"></div>
        </section>

        {{-- FEATURES --}}
        <section id="features" class="relative py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <span class="text-sm font-semibold text-primary-400 uppercase tracking-widest">{{ __('TUDO QUE VOCÊ PRECISA') }}</span>
                    <h2 class="mt-4 text-4xl sm:text-5xl font-black text-white">{{ __('Recursos Poderosos') }}</h2>
                    <p class="mt-4 text-lg text-slate-400 max-w-2xl mx-auto">{{ __('Ferramentas completas para transformar a gestão do seu restaurante.') }}</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                        $features = [
                            ['icon' => 'fa-clipboard-list', 'title' => 'Gestão de Pedidos', 'desc' => 'Acompanhe pedidos em tempo real com kanban, notificações e histórico completo.'],
                            ['icon' => 'fa-book', 'title' => 'Cardápio Digital', 'desc' => 'Crie cardápios diários com pratos, categorias, tamanhos e preços personalizados.'],
                            ['icon' => 'fa-chart-line', 'title' => 'Relatórios', 'desc' => 'Acompanhe receitas, pratos mais vendidos e horários de pico com gráficos.'],
                            ['icon' => 'fa-users', 'title' => 'Clientes', 'desc' => 'Gerencie sua base de clientes com histórico de pedidos e valores gastos.'],
                            ['icon' => 'fa-truck', 'title' => 'Entregas', 'desc' => 'Controle entregas, taxas e horários com integração de endereços.'],
                            ['icon' => 'fa-shield-halved', 'title' => 'Multiusuário', 'desc' => 'Níveis de acesso root, admin e usuário para equipes organizadas.'],
                        ];
                    @endphp

                    @foreach ($features as $f)
                        <div class="feature-card group relative bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-8 hover:bg-slate-900/80 hover:border-primary-500/30 transition-all duration-300 hover:shadow-xl hover:shadow-primary-600/5 hover:-translate-y-1">
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-500/20 to-primary-700/20 flex items-center justify-center mb-5 group-hover:from-primary-500/30 group-hover:to-primary-700/30 transition-all">
                                <i class="fa-solid {{ $f['icon'] }} text-2xl text-primary-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">{{ __($f['title']) }}</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">{{ __($f['desc']) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- STATS --}}
        <section id="stats" class="relative py-32 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-primary-950/20 to-slate-950"></div>
            <div class="absolute inset-0 opacity-10"
                 style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.05) 1px, transparent 0); background-size: 40px 40px;">
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-black text-white">{{ __('Números que falam') }}</h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    @php
                        $stats = [
                            ['value' => '500+', 'label' => 'Restaurantes Ativos', 'icon' => 'fa-store'],
                            ['value' => '50K+', 'label' => 'Pedidos Realizados', 'icon' => 'fa-receipt'],
                            ['value' => '98%', 'label' => 'Satisfação', 'icon' => 'fa-face-smile'],
                            ['value' => '24/7', 'label' => 'Suporte', 'icon' => 'fa-headset'],
                        ];
                    @endphp

                    @foreach ($stats as $s)
                        <div class="stat-item text-center group">
                            <div class="w-16 h-16 rounded-2xl bg-slate-800/50 border border-slate-700/50 flex items-center justify-center mx-auto mb-4 group-hover:border-primary-500/40 group-hover:bg-slate-800/80 transition-all">
                                <i class="fa-solid {{ $s['icon'] }} text-2xl text-primary-400"></i>
                            </div>
                            <div class="text-4xl font-black text-white mb-1">{{ $s['value'] }}</div>
                            <div class="text-sm text-slate-400">{{ __($s['label']) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section id="cta" class="relative py-32">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-primary-950/10 to-primary-950/30"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center cta-section">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-500/10 border border-primary-500/20 rounded-full text-sm text-primary-300 mb-8">
                    <i class="fa-solid fa-rocket"></i>
                    {{ __('Comece agora') }}
                </div>
                <h2 class="text-4xl sm:text-5xl font-black text-white max-w-3xl mx-auto leading-tight">
                    {{ __('Pronto para transformar seu restaurante?') }}
                </h2>
                <p class="mt-4 text-lg text-slate-400 max-w-xl mx-auto">
                    {{ __('Solicite acesso e descubra como o MenuHub pode simplificar sua gestão.') }}
                </p>
                <div class="mt-10">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2.5 px-8 py-4 bg-primary-600 text-white font-bold text-base rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-600/30 hover:shadow-2xl hover:shadow-primary-600/40 hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fa-solid fa-gauge-high"></i>
                                {{ __('Ir para o Painel') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2.5 px-8 py-4 bg-primary-600 text-white font-bold text-base rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-600/30 hover:shadow-2xl hover:shadow-primary-600/40 hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                {{ __('Acessar o Sistema') }}
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </section>

        {{-- FOOTER --}}
        <footer class="border-t border-slate-800 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <x-logo dark class="h-10" />
                    <p class="text-sm text-slate-500">
                        &copy; {{ date('Y') }} MenuHub. {{ __('Todos os direitos reservados.') }}
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        .animation-delay-200 { animation-delay: 0.2s; }
        .animation-delay-400 { animation-delay: 0.4s; }
        .animation-delay-600 { animation-delay: 0.6s; }
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
