@props(['title', 'code', 'message', 'icon' => 'fa-exclamation-circle'])

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - {{ __('MenuHub') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-950 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg text-center">
        <a href="/" class="inline-flex items-center justify-center mb-6">
            <x-logo dark class="h-9" />
        </a>

        <div class="bg-card dark:bg-card-dark backdrop-blur-sm shadow-card sm:rounded-2xl border border-border dark:border-border-dark p-10">
            <div class="text-8xl font-black text-primary-600 dark:text-primary-400 leading-none mb-2">
                {{ $code }}
            </div>

            <div class="text-5xl text-slate-300 dark:text-slate-600 mb-4">
                <i class="fas {{ $icon }}"></i>
            </div>

            <h1 class="text-xl font-bold text-text-primary dark:text-text-dark mb-2">{{ $title }}</h1>

            <p class="text-text-secondary dark:text-slate-400 text-sm mb-6">{{ $message }}</p>

            <div class="flex items-center justify-center gap-2 mb-6 text-sm text-text-secondary dark:text-slate-400">
                <i class="fas fa-redo-alt text-primary-500"></i>
                <span>{{ __('Redirecionando em') }}</span>
                <span id="countdown" class="font-bold text-lg text-primary-600 dark:text-primary-400 min-w-[2ch]">10</span>
                <span>{{ __('s') }}</span>
            </div>

            <div class="flex items-center justify-center gap-3">
                <a href="{{ url()->previous() !== url()->current() && url()->previous() !== '' ? url()->previous() : '/' }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-150 border border-border dark:border-border-dark bg-card dark:bg-card-dark text-text-primary dark:text-text-dark shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Voltar') }}
                </a>
                <a href="/"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-150 bg-primary-600 text-white shadow-sm hover:bg-primary-700 hover:shadow-md hover:shadow-primary-600/20 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <i class="fas fa-home"></i>
                    {{ __('Página Inicial') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var seconds = 10;
            var el = document.getElementById('countdown');
            if (el) {
                var interval = setInterval(function() {
                    seconds--;
                    el.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(interval);
                        window.location.href = '/';
                    }
                }, 1000);
            }
        })();
    </script>
</body>
</html>
