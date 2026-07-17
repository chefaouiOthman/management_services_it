@php
    function permissionDescription($permName) {
        $parts = explode('-', $permName);
        $resource = $parts[0] ?? '';
        $action = $parts[1] ?? '';
        $resourceLabels = [
            'user' => 'utilisateur', 'employe' => 'employé', 'stagiaire' => 'stagiaire', 'client' => 'client',
            'departement' => 'département', 'contrat' => 'contrat', 'zone' => "zone d'accès", 'historique-passage' => "historique de passages", 'pointage' => 'pointage',
            'projet' => 'projet', 'tache' => 'tâche', 'feuille-temps' => 'feuille de temps', 'livrable' => 'livrable', 'technologie' => 'technologie',
            'catalogue-formation' => 'catalogue de formation', 'session-formation' => 'session de formation', 'inscription' => 'inscription', 'support-cours' => 'support de cours', 'evaluation' => 'évaluation',
            'type-materiel' => "type de matériel", 'asset' => 'matériel IT', 'ticket' => 'ticket de maintenance', 'licence' => 'licence logiciel', 'assignation-materiel' => "assignation de matériel", 'assignation-licence' => "assignation de licence",
            'categorie-flux' => 'catégorie de flux', 'flux-tresorerie' => 'flux de trésorerie', 'facture' => 'facture', 'ligne-facture' => 'ligne de facture', 'fiche-paie' => 'fiche de paie', 'note-de-frais' => 'note de frais',
            'role' => 'rôle',
        ];
        $actionLabels = [
            'view' => 'Consulter la liste',
            'create' => 'Créer',
            'edit' => 'Modifier',
            'delete' => 'Supprimer',
        ];
        $r = $resourceLabels[$resource] ?? $resource;
        $a = $actionLabels[$action] ?? $action;
        return "Permet de {$a} un(e) {$r}.";
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#1E293B] leading-tight tracking-tight">
                {{ __('Créer un Rôle') }}
            </h2>
            <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-[#475569] bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <form method="POST" action="{{ route('roles.store') }}" class="space-y-6">
            @csrf

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-[#1E293B]">Nom du Rôle</h3>
                </x-slot>
                <div class="max-w-md">
                    <x-input-label for="name" value="Nom du rôle" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required placeholder="Ex: Chef de Projet" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-[#1E293B]">Permissions</h3>
                    <p class="text-sm text-gray-500 mt-1">Cochez les permissions à attribuer à ce rôle. Les permissions marquées <span class="text-red-600 font-semibold">Super Admin</span> sont réservées.</p>
                </x-slot>
                @if($permissions->isNotEmpty())
                    @php
                        $grouped = $permissions->groupBy(function($perm) {
                            return explode('-', $perm->name)[0] ?? $perm->name;
                        });
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($grouped as $resource => $perms)
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <h4 class="text-sm font-semibold text-[#1E293B] uppercase tracking-wider mb-3">{{ $resource }}</h4>
                                <div class="space-y-2">
                                    @foreach($perms as $perm)
                                        @php
                                            $action = explode('-', $perm->name)[1] ?? '';
                                            $desc = permissionDescription($perm->name);
                                            $isRolePermission = str_starts_with($perm->name, 'role-');
                                        @endphp
                                        <div x-data="{ open: false }" class="flex items-center gap-1">
                                            <input type="checkbox" name="permission[]" value="{{ $perm->name }}" id="perm_{{ $loop->parent->index }}_{{ $loop->index }}"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                {{ $isRolePermission ? 'disabled' : '' }}
                                                {{ in_array($perm->name, old('permission', $rolePermissions->toArray() ?? [])) ? 'checked' : '' }}>
                                            <label for="perm_{{ $loop->parent->index }}_{{ $loop->index }}" class="text-sm {{ $isRolePermission ? 'text-gray-400 line-through' : 'text-gray-700' }} cursor-pointer select-none">
                                                {{ $action }}
                                            </label>
                                            @if($isRolePermission)
                                            <span class="ml-1 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 bg-red-50 rounded">Super Admin</span>
                                            @endif
                                            <button @click="open = !open" type="button" class="ml-auto text-gray-400 hover:text-indigo-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" class="relative">
                                                <div class="absolute z-10 right-0 mt-1 w-64 px-3 py-2 text-xs text-white bg-gray-800 rounded-lg shadow-lg">
                                                    {{ $desc }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucune permission disponible.</p>
                @endif
            </x-card>

            <div class="flex justify-end gap-3">
                <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-[#475569] bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-br from-indigo-600 to-violet-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:from-indigo-700 hover:to-violet-700 active:scale-[0.98] transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Créer le Rôle
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
