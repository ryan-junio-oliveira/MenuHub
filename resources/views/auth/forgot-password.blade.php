@php $title = __('Redefinir Senha'); @endphp

<x-guest-layout>
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-primary-500/15 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-lock text-2xl text-primary-400"></i>
        </div>
        <h1 class="text-2xl font-bold text-white">{{ __('Esqueceu a senha?') }}</h1>
        <p class="text-sm text-slate-400 mt-1">{{ __('Informe seu e-mail e enviaremos um link de redefinição.') }}</p>
    </div>

    <x-auth-session-status class="mb-4 text-green-400 text-sm" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="{{ __('E-mail') }}" />
            <input id="email" class="input-field" type="email" name="email" :value="old('email')" required autofocus placeholder="{{ __('seu@email.com') }}" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="mt-7">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-primary-600 text-white font-bold text-sm rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/25 hover:shadow-xl hover:shadow-primary-600/30 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500">
                <i class="fa-solid fa-paper-plane"></i>
                {{ __('Enviar Link') }}
            </button>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-primary-400 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                {{ __('Voltar ao login') }}
            </a>
        </div>
    </form>
</x-guest-layout>
