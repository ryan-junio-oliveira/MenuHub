<x-card padding="6">
    <div class="flex items-center gap-2 mb-6">
        <i class="fa-regular fa-user text-base text-primary-600"></i>
        <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">Informações do Perfil</h3>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" value="Nome" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full input-field" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" value="E-mail" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full input-field" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-button variant="primary">
                <i class="fa-regular fa-circle-check text-sm"></i>
                Salvar
            </x-button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-green-600 dark:text-green-400">Salvo.</p>
            @endif
        </div>
    </form>
</x-card>
