<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('MenuHub') }} — {{ __('MenuHub') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth-animations.js'])
</head>
<body class="font-sans antialiased bg-slate-950 text-white min-h-screen flex">

    <div class="flex w-full min-h-screen">

        {{-- LEFT PANEL — Branding (hidden on mobile) --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-slate-950 via-primary-950 to-slate-950 overflow-hidden">

            {{-- Three.js Canvas --}}
            <canvas id="auth-canvas" class="absolute inset-0 w-full h-full"></canvas>

            {{-- Grid overlay --}}
            <div class="absolute inset-0 opacity-15"
                 style="background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px); background-size: 50px 50px;">
            </div>

            {{-- Orbs --}}
            <div class="absolute top-1/4 -left-10 w-80 h-80 bg-primary-500/15 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-1/3 right-10 w-60 h-60 bg-primary-400/10 rounded-full blur-[80px]"></div>

            {{-- Floating shapes --}}
            <div class="absolute top-20 right-20 w-16 h-16 border border-primary-500/20 rounded-xl rotate-45 animate-float-slow opacity-30"></div>
            <div class="absolute bottom-32 left-16 w-20 h-20 border border-primary-400/15 rounded-full animate-float-slow opacity-20" style="animation-delay: 2s"></div>
            <div class="absolute top-1/2 left-1/4 w-12 h-12 bg-primary-500/10 rounded-lg rotate-12 animate-float-slow opacity-25" style="animation-delay: 1s"></div>

            {{-- Content --}}
            <div class="relative z-10 flex flex-col w-full p-12 lg:p-16 xl:p-20">
                {{-- Logo --}}
                <div class="mb-16">
                    <x-logo dark class="h-11" />
                </div>

                {{-- Center content --}}
                <div class="flex-1 flex flex-col justify-center max-w-lg">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary-500/10 border border-primary-500/20 rounded-full text-xs font-medium text-primary-300 mb-6 w-fit">
                        <span class="w-1.5 h-1.5 bg-primary-400 rounded-full animate-pulse"></span>
                        {{ __('Gestão inteligente') }}
                    </div>

                    <h2 class="text-4xl xl:text-5xl font-black leading-tight mb-4">
                        <span class="bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">{{ __('Cardápio, Pedidos') }}</span>
                        <br>
                        <span class="bg-gradient-to-r from-primary-400 via-primary-500 to-primary-600 bg-clip-text text-transparent">{{ __('e Gestão') }}</span>
                    </h2>

                    <p class="text-slate-400 text-base leading-relaxed mb-10">
                        {{ __('Centralize seus cardápios, receba pedidos em tempo real e acompanhe tudo em um só lugar.') }}
                    </p>

                    {{-- Feature bullets --}}
                    <div class="space-y-4">
                        @php
                            $bullets = [
                                ['icon' => 'fa-clipboard-list', 'text' => 'Gestão de pedidos em tempo real'],
                                ['icon' => 'fa-book', 'text' => 'Cardápios diários personalizados'],
                                ['icon' => 'fa-chart-line', 'text' => 'Relatórios e estatísticas completas'],
                                ['icon' => 'fa-users', 'text' => 'Níveis de acesso root, admin e usuário'],
                            ];
                        @endphp
                        @foreach ($bullets as $b)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-primary-500/15 flex items-center justify-center shrink-0">
                                    <i class="fa-solid {{ $b['icon'] }} text-xs text-primary-400"></i>
                                </div>
                                <span class="text-sm text-slate-300">{{ __($b['text']) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Footer --}}
                <div class="mt-auto pt-12 text-xs text-slate-600">
                    &copy; {{ date('Y') }} MenuHub.
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL — Form --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-8 bg-slate-950 lg:bg-slate-950/50">
            {{-- Mobile logo --}}
            <div class="lg:hidden fixed top-0 left-0 right-0 z-50 flex items-center justify-center py-4 bg-slate-950/90 backdrop-blur-lg border-b border-slate-800">
                <a href="/">
                    <x-logo dark class="h-10" />
                </a>
            </div>

            <div class="w-full max-w-2xl mt-16 lg:mt-0">
                {{-- Desktop logo hidden on mobile --}}
                <div class="hidden lg:block mb-8">
                    <x-logo dark class="h-11" />
                </div>

                <div class="auth-form-card bg-slate-900/70 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/40">
                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(3deg); }
        }
        .animate-float-slow {
            animation: float-slow 7s ease-in-out infinite;
        }
    </style>
</body>
</html>
