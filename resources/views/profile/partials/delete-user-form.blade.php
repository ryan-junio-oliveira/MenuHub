<x-card padding="6" class="border-red-200 dark:border-red-900">
    <div class="flex items-center gap-2 mb-6">
        <i class="fa-solid fa-triangle-exclamation text-base text-red-600"></i>
        <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">Excluir Conta</h3>
    </div>

    <p class="text-sm text-text-secondary dark:text-slate-400 mb-6">
        Ao excluir sua conta, todos os dados serão permanentemente removidos. Esta ação não pode ser desfeita.
    </p>

    <div x-data="{ confirmDelete: false }">
        <button type="button" x-on:click="confirmDelete = true" class="btn-danger">
            <i class="fa-solid fa-trash-can text-sm"></i>
            Excluir Conta
        </button>

        <div x-show="confirmDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" x-on:click="confirmDelete = false"></div>
            <div class="relative bg-card dark:bg-card-dark rounded-2xl border border-border dark:border-border-dark shadow-xl p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark mb-2">Tem certeza?</h3>
                <p class="text-sm text-text-secondary mb-6">Digite sua senha para confirmar a exclusão da conta.</p>
                <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                    @csrf
                    @method('DELETE')
                    <div>
                        <x-input-label for="password" value="Senha" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full input-field" placeholder="Digite sua senha" />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" x-on:click="confirmDelete = false" class="btn-secondary">Cancelar</button>
                        <button type="submit" class="btn-danger">Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-card>
