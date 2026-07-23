@php $title = __('Criar Conta'); @endphp

<x-guest-layout>
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-white">{{ __('Criar Conta') }}</h1>
        <p class="text-sm text-slate-400 mt-1">{{ __('Preencha os dados para se cadastrar') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="{{ __('Nome') }}" />
            <input id="name" class="input-field" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="{{ __('Seu nome completo') }}" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div class="mt-5">
            <x-input-label for="email" value="{{ __('E-mail') }}" />
            <input id="email" class="input-field" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="{{ __('seu@email.com') }}" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="mt-5">
            <x-input-label for="password" value="{{ __('Senha') }}" />
            <input id="password" class="input-field" type="password" name="password" required autocomplete="new-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="mt-5">
            <x-input-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
            <input id="password_confirmation" class="input-field" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="mt-7">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-primary-600 text-white font-bold text-sm rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/25 hover:shadow-xl hover:shadow-primary-600/30 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500">
                <i class="fa-solid fa-user-plus"></i>
                {{ __('Criar Conta') }}
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
