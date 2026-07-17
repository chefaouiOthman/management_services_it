<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#1E293B] leading-tight tracking-tight">
                {{ __('Gestion des Rôles') }}
            </h2>
            @can('role-create')
            <a href="{{ route('roles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-indigo-600 to-violet-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:from-indigo-700 hover:to-violet-700 active:scale-[0.98] transition-all duration-200 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nouveau Rôle
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <x-search-filters :search="request('search')" searchPlaceholder="Rechercher par nom de rôle..." :filters="[]" />

        <div class="space-y-4">
            @forelse($roles as $role)
                <x-card>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-white flex items-center justify-center font-bold shadow-sm text-sm">
                                {{ substr($role->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-[#1E293B]">{{ $role->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $role->permissions->count() }} permission(s) associée(s)</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('roles.show', $role->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                Voir
                            </a>
                            @can('role-edit')
                            <a href="{{ route('roles.edit', $role->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                                Modifier
                            </a>
                            @endcan
                            @can('role-delete')
                            @if($role->name !== 'Super Admin')
                            <form method="POST" action="{{ route('roles.destroy', $role->id) }}" onsubmit="return confirm('Supprimer ce rôle ? Les utilisateurs perdront ce rôle.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                    Supprimer
                                </button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </div>
                </x-card>
            @empty
                <x-card>
                    <p class="text-center text-gray-500 py-4">Aucun rôle trouvé.</p>
                </x-card>
            @endforelse

            {{ $roles->appends(request()->query())->links() }}
        </div>
    </div>
</x-app-layout>
