<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#1E293B] leading-tight tracking-tight">
                {{ __('Mon Profil') }}
            </h2>
            @php $roleName = auth()->user()->roles->pluck('name')->join(', '); @endphp
            <x-badge type="indigo">{{ $roleName ?: 'Non défini' }}</x-badge>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{ editMode: false }">

        {{-- ================================================================ --}}
        {{-- SECTION READ-ONLY — visible par tous les rôles                    --}}
        {{-- ================================================================ --}}

        <div x-show="!editMode" x-transition>
            <div class="space-y-6">

                {{-- ========== BLOC 1 : Informations du Profil ========== --}}
                <x-card>
                    <x-slot name="header">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-white flex items-center justify-center font-bold shadow-sm">
                                {{ substr($user->nom_complet ?? '?', 0, 1) }}
                            </div>
                            <h3 class="text-lg font-semibold text-[#1E293B]">Informations du Profil</h3>
                        </div>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <x-input-label value="Nom Complet" />
                            <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $user->nom_complet ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <x-input-label value="Adresse Email" />
                            <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $user->email ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <x-input-label value="CIN" />
                            <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $user->cin ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <x-input-label value="Statut du Compte" />
                            <div class="mt-1">
                                @if($user->est_actif)
                                    <x-badge type="success">Actif</x-badge>
                                @else
                                    <x-badge type="danger">Inactif</x-badge>
                                @endif
                            </div>
                        </div>
                        <div>
                            <x-input-label value="Membre depuis" />
                            <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $user->created_at?->format('d/m/Y') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </x-card>

                {{-- ========== BLOC 2 & 3 : EMPLOYÉ / ADMIN ========== --}}
                @if($user->employe)
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-[#1E293B]">Informations Professionnelles</h3>
                        </x-slot>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label value="Département" />
                                <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $user->employe?->departement?->nom_departement ?? 'Non affecté' }}</p>
                            </div>
                            <div>
                                <x-input-label value="Date d'embauche" />
                                <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $user->employe?->date_embauche?->format('d/m/Y') ?? 'Non définie' }}</p>
                            </div>
                        </div>
                    </x-card>

                    @php $contratActuel = $user->employe?->contratActuel; @endphp
                    @if($contratActuel)
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-semibold text-[#1E293B]">Caractéristiques du Contrat</h3>
                            </x-slot>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label value="Type de Contrat" />
                                    <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $contratActuel->type_contrat ?? 'Non défini' }}</p>
                                </div>
                                <div>
                                    <x-input-label value="Date de Début" />
                                    <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $contratActuel->date_debut?->format('d/m/Y') ?? 'Non définie' }}</p>
                                </div>
                                <div>
                                    <x-input-label value="Date de Fin" />
                                    <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $contratActuel->date_fin?->format('d/m/Y') ?? 'Non définie' }}</p>
                                </div>
                                <div>
                                    <x-input-label value="Salaire de Base" />
                                    <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $contratActuel->salaire_base ? number_format($contratActuel->salaire_base, 2, ',', ' ') . ' MAD' : 'Non défini' }}</p>
                                </div>
                                <div>
                                    <x-input-label value="Heures Hebdomadaires" />
                                    <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $contratActuel->heures_hebdo ?? 'Non défini' }} h</p>
                                </div>
                                <div>
                                    <x-input-label value="Statut du Contrat" />
                                    <div class="mt-1">
                                        @php $statut = $contratActuel->statut ?? 'inconnu'; @endphp
                                        @if($statut === 'actif')
                                            <x-badge type="success">Actif</x-badge>
                                        @elseif($statut === 'suspendu')
                                            <x-badge type="danger">Suspendu</x-badge>
                                        @elseif($statut === 'termine')
                                            <x-badge type="gray">Terminé</x-badge>
                                        @else
                                            <x-badge type="gray">{{ ucfirst($statut) }}</x-badge>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </x-card>
                    @endif
                @endif

                {{-- ========== BLOC STAGIAIRE ========== --}}
                @if($user->stagiaire)
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-[#1E293B]">Informations Stagiaire</h3>
                        </x-slot>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label value="École d'origine" />
                                <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $user->stagiaire?->ecole_origine ?? 'Non renseignée' }}</p>
                            </div>
                            <div>
                                <x-input-label value="Département" />
                                <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $user->stagiaire?->departement?->nom_departement ?? 'Non affecté' }}</p>
                            </div>
                            <div class="col-span-full">
                                <x-input-label value="Sujet de stage" />
                                <p class="mt-1 text-base font-medium text-[#1E293B] whitespace-pre-wrap">{{ $user->stagiaire?->sujet_stage ?? 'Non renseigné' }}</p>
                            </div>
                        </div>
                    </x-card>
                @endif

                {{-- ========== BLOC CLIENT ========== --}}
                @if($user->client)
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-[#1E293B]">Informations Client</h3>
                        </x-slot>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label value="Type de client" />
                                <p class="mt-1 text-base font-medium text-[#1E293B] capitalize">{{ $user->client?->type_client ?? 'Non défini' }}</p>
                            </div>
                            <div>
                                <x-input-label value="Nom Société" />
                                <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $user->client?->nom_societe ?? 'Non renseigné' }}</p>
                            </div>
                            <div>
                                <x-input-label value="ICE" />
                                <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $user->client?->ice ?? 'Non renseigné' }}</p>
                            </div>
                        </div>
                    </x-card>
                @endif

                {{-- ========== BOUTON MODIFIER (Admin uniquement) ========== --}}
                @if(auth()->user()->hasRole('Admin'))
                    <div class="flex justify-center pt-2">
                        <button @click="editMode = true" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-br from-indigo-600 to-violet-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:from-indigo-700 hover:to-violet-700 active:scale-[0.98] transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Modifier mon profil
                        </button>
                    </div>
                @endif

            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- SECTION ADMIN : FORMULAIRES BREEZE (modification)                --}}
        {{-- ================================================================ --}}

        @if(auth()->user()->hasRole('Admin'))
            <div x-show="editMode" x-transition>
                <div class="space-y-6">

                    {{-- Bouton retour lecture seule --}}
                    <div class="flex justify-start">
                        <button @click="editMode = false" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[#475569] bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 active:scale-[0.98] transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Retour à la fiche profil
                        </button>
                    </div>

                    {{-- Formulaire infos profil --}}
                    <div class="p-4 sm:p-8 bg-white shadow-sm rounded-xl border border-gray-100">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- Formulaire mot de passe --}}
                    <div class="p-4 sm:p-8 bg-white shadow-sm rounded-xl border border-gray-100">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- Suppression de compte --}}
                    <div class="p-4 sm:p-8 bg-white shadow-sm rounded-xl border border-gray-100">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>
            </div>
        @endif

    </div>
</x-app-layout>