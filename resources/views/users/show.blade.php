<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Détails du Profil : ') }} {{ $user->nom_complet }}
            </h2>
            <div class="flex space-x-3">
                @can('user-edit')
                <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Modifier
                </a>
                @endcan
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Retour à l'annuaire
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profil Général -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informations Générales</h3>
                </x-slot>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nom Complet</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $user->nom_complet }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Rôles d'accès</p>
                        <div class="mt-1 flex gap-2">
                            @forelse($user->roles as $role)
                                <x-badge type="gray">{{ $role->name }}</x-badge>
                            @empty
                                <span class="text-sm text-gray-500">Aucun rôle défini</span>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Statut du compte</p>
                        <div class="mt-1">
                            @if($user->est_actif)
                                <x-badge type="success">Actif</x-badge>
                            @else
                                <x-badge type="danger">Inactif</x-badge>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Spécifique Employé & Contrat -->
            @if($user->employe)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Dossier Employé</h3>
                        </x-slot>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">CIN</p>
                                <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $user->employe->CIN }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Date d'embauche</p>
                                <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $user->employe->date_embauche ? $user->employe->date_embauche->format('d/m/Y') : 'Non définie' }}</p>
                            </div>
                        </div>
                    </x-card>

                    @if($contratActuel = $user->employe->contratActuel)
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Contrat Actuel</h3>
                            </x-slot>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Type de contrat</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $contratActuel->type_contrat }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Statut</p>
                                    <div class="mt-1">
                                        @if($contratActuel->statut == 'actif')
                                            <x-badge type="success">Actif</x-badge>
                                        @elseif($contratActuel->statut == 'suspendu')
                                            <x-badge type="danger">Suspendu</x-badge>
                                        @else
                                            <x-badge type="gray">Terminé</x-badge>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Date de début</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $contratActuel->date_debut?->format('d/m/Y') ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Date de fin</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-gray-100">
                                        @if($contratActuel->date_fin)
                                            {{ $contratActuel->date_fin->format('d/m/Y') }}
                                        @else
                                            <span class="italic text-gray-400">Indéterminée</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Salaire de base</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ number_format($contratActuel->salaire_base, 2, ',', ' ') }} MAD</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Heures hebdo.</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $contratActuel->heures_hebdo }} h</p>
                                </div>
                            </div>
                        </x-card>
                    @endif
                </div>
            @endif

            <!-- Spécifique Stagiaire -->
            @if($user->stagiaire)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Dossier Stagiaire</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">École d'origine</p>
                            <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $user->stagiaire->ecole_origine }}</p>
                        </div>
                        <div class="col-span-full">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sujet de stage</p>
                            <p class="text-base font-medium text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $user->stagiaire->sujet_stage }}</p>
                        </div>
                    </div>
                </x-card>
            @endif

            <!-- Spécifique Client -->
            @if($user->client)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Dossier Client</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Type de client</p>
                            <p class="text-base font-medium text-gray-900 dark:text-gray-100 capitalize">{{ $user->client->type_client }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nom Société</p>
                            <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $user->client->nom_societe ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">ICE</p>
                            <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $user->client->ice ?? '-' }}</p>
                        </div>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
