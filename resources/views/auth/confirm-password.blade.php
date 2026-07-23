@php $title = __('Confirmar Senha'); @endphp

<x-guest-layout>
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-primary-500/15 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-shield-halved text-2xl text-primary-400"></i>
        </div>
        <h1 class="text-2xl font-bold text-white">{{ __('Área Segura') }}</h1>
        <p class="text-sm text-slate-400 mt-1">{{ __('Confirme sua senha antes de continuar.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" value="{{ __('Senha') }}" />
            <input id="password" class="input-field" type="password" name="password" required autocomplete="current-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="mt-7">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-primary-600 text-white font-bold text-sm rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/25 hover:shadow-xl hover:shadow-primary-600/30 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500">
                <i class="fa-solid fa-check"></i>
                {{ __('Confirmar') }}
            </button>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ url()->previous() !== url()->current() && url()->previous() !== '' ? url()->previous() : route('dashboard') }}"
               class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-primary-400 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                {{ __('Voltar') }}
            </a>
        </div>
    </form>
</x-guest-layout>
