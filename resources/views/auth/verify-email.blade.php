@php $title = __('Verificar E-mail'); @endphp

<x-guest-layout>
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-primary-500/15 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-envelope text-2xl text-primary-400"></i>
        </div>
        <h1 class="text-2xl font-bold text-white">{{ __('Verifique seu E-mail') }}</h1>
        <p class="text-sm text-slate-400 mt-1">{{ __('Antes de começar, verifique seu e-mail clicando no link que enviamos.') }}</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-400 text-center">
            {{ __('Um novo link de verificação foi enviado para o e-mail informado no cadastro.') }}
        </div>
    @endif

    <div class="flex flex-col gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-primary-600 text-white font-bold text-sm rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/25 hover:shadow-xl hover:shadow-primary-600/30 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500">
                <i class="fa-solid fa-paper-plane"></i>
                {{ __('Reenviar E-mail') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 border border-slate-700 text-slate-300 font-semibold text-sm rounded-xl hover:border-slate-600 hover:text-white transition-all active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-slate-500">
                <i class="fa-solid fa-right-from-bracket"></i>
                {{ __('Sair') }}
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-800">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-primary-400 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                {{ __('Ir para o painel') }}
            </a>
        </div>
    </div>
</x-guest-layout>
