<x-card padding="6">
    <div class="flex items-center gap-2 mb-6">
        <i class="fa-solid fa-lock text-base text-primary-600"></i>
        <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">Atualizar Senha</h3>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <x-input-label for="current_password" value="Senha Atual" />
                <x-text-input id="current_password" name="current_password" type="password" placeholder="Senha atual" class="mt-1 block w-full input-field" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="new_password" value="Nova Senha" />
                <x-text-input id="new_password" name="password" type="password" placeholder="Nova senha" class="mt-1 block w-full input-field" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirmar Senha" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirme a senha" class="mt-1 block w-full input-field" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-button variant="primary">
                <i class="fa-regular fa-circle-check text-sm"></i>
                Salvar
            </x-button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-green-600 dark:text-green-400">Salvo.</p>
            @endif
        </div>
    </form>
</x-card>
