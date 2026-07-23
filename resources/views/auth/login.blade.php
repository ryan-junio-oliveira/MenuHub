@php $title = __('Entrar'); @endphp

<x-guest-layout>
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-white">{{ __('Bem-vindo de volta') }}</h1>
        <p class="text-sm text-slate-400 mt-1">{{ __('Faça login para acessar o sistema') }}</p>
    </div>

    <x-auth-session-status class="mb-4 text-green-400 text-sm" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="{{ __('E-mail') }}" />
            <input id="email" class="input-field" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="{{ __('seu@email.com') }}" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="mt-5">
            <x-input-label for="password" value="{{ __('Senha') }}" />
            <input id="password" class="input-field" type="password" name="password" required autocomplete="current-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between mt-5">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-600 bg-slate-800 text-primary-600 shadow-sm focus:ring-primary-500 focus:ring-offset-0" name="remember">
                <span class="text-sm text-slate-300">{{ __('Lembrar-me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-primary-400 hover:text-primary-300 transition-colors" href="{{ route('password.request') }}">
                    {{ __('Esqueceu a senha?') }}
                </a>
            @endif
        </div>

        <div class="mt-7">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-primary-600 text-white font-bold text-sm rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/25 hover:shadow-xl hover:shadow-primary-600/30 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500">
                <i class="fa-solid fa-right-to-bracket"></i>
                {{ __('Entrar') }}
            </button>
        </div>

        <div class="mt-6 text-center">
            <a href="/" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-primary-400 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                {{ __('Voltar ao início') }}
            </a>
        </div>
    </form>

    <p class="mt-6 text-center text-xs text-slate-600 leading-relaxed">
        {{ __('O acesso ao sistema é concedido pelo administrador.') }}
    </p>
</x-guest-layout>
