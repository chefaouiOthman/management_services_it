<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Session : {{ $session->catalogueFormation->titre_formation }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Du {{ \Carbon\Carbon::parse($session->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($session->date_fin)->format('d/m/Y') }}</p>
            </div>
            <div class="flex gap-2">
                @can('session-formation-edit')
                <a href="{{ route('sessions.edit', $session->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-300 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition">
                    Modifier Session
                </a>
                @endcan
                <a href="{{ route('sessions.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'details' }" @hashchange.window="tab = window.location.hash.substring(1)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Inscrits</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $session->inscriptions->count() }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Formateurs</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $session->formateurs->count() }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Évaluations</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $session->evaluations->count() }}</p>
                </x-card>
            </div>

            <!-- Navigation Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#details" :class="tab === 'details' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Détails & Formateurs</a>
                    <a href="#inscriptions" :class="tab === 'inscriptions' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Inscriptions (Apprenants)</a>
                    <a href="#evaluations" :class="tab === 'evaluations' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Évaluations</a>
                </nav>
            </div>

            <!-- TAB: DETAILS & FORMATEURS -->
            <div x-show="tab === 'details'" x-cloak class="space-y-4">
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Lieux et Accès</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Salle Physique</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $session->salle_concrete ?? 'Non définie' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lien Visio</p>
                            @if($session->salle_virtuelle)
                                <a href="{{ $session->salle_virtuelle }}" target="_blank" class="text-indigo-600 hover:underline break-all">{{ $session->salle_virtuelle }}</a>
                            @else
                                <p class="font-medium text-gray-900 dark:text-gray-100">Non défini</p>
                            @endif
                        </div>
                    </div>
                </x-card>

                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-6">Équipe Pédagogique (Formateurs)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($session->formateurs as $formateur)
                        <x-card class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xl uppercase">
                                {{ substr($formateur->user->nom_complet, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $formateur->user->nom_complet }}</p>
                                <p class="text-xs text-gray-500">{{ $formateur->user->email }}</p>
                            </div>
                        </x-card>
                    @empty
                        <div class="col-span-full py-4 text-gray-500 italic">Aucun formateur assigné à cette session.</div>
                    @endforelse
                </div>
            </div>

            <!-- TAB: INSCRIPTIONS -->
            <div x-show="tab === 'inscriptions'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Liste des Apprenants</h3>
                    @can('inscription-create')
                    <a href="{{ route('inscriptions.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                        + Ajouter Inscription
                    </a>
                    @endcan
                </div>

                <x-card>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Apprenant</th>
                                    <th scope="col" class="px-6 py-3">Type</th>
                                    <th scope="col" class="px-6 py-3 text-center">Statut Actuel</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions Rapides (Changer Statut)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($session->inscriptions as $inscription)
                                    @php
                                        // Déterminer le type d'utilisateur (Employé, Stagiaire, Client) basé sur les relations si nécessaires, 
                                        // ou on affiche simplement l'email ou un attribut générique ici.
                                        $typeStr = 'Utilisateur';
                                        if($inscription->user->employe) $typeStr = 'Employé';
                                        elseif($inscription->user->stagiaire) $typeStr = 'Stagiaire';
                                        elseif($inscription->user->client) $typeStr = 'Client';
                                    @endphp
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" 
                                        x-data="inscriptionStatus({{ $inscription->id }}, '{{ $inscription->statut_inscription }}')">
                                        
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            {{ $inscription->user->nom_complet }}
                                            <br><span class="text-xs text-gray-500 font-normal">{{ $inscription->user->email }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                                            {{ $typeStr }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full border font-medium uppercase"
                                                  :class="{
                                                      'bg-gray-100 text-gray-700 border-gray-300': currentStatut === 'annule',
                                                      'bg-blue-100 text-blue-700 border-blue-300': currentStatut === 'valide',
                                                      'bg-yellow-100 text-yellow-700 border-yellow-300': currentStatut === 'present',
                                                      'bg-green-100 text-green-700 border-green-300': currentStatut === 'certifie'
                                                  }"
                                                  x-text="currentStatut">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @can('inscription-edit')
                                            <div class="flex justify-end gap-1 relative">
                                                <button @click="updateStatut('valide')" :disabled="loading" class="px-2 py-1 text-xs bg-white border border-blue-200 text-blue-600 rounded hover:bg-blue-50 disabled:opacity-50">Valide</button>
                                                <button @click="updateStatut('present')" :disabled="loading" class="px-2 py-1 text-xs bg-white border border-yellow-200 text-yellow-600 rounded hover:bg-yellow-50 disabled:opacity-50">Présent</button>
                                                <button @click="updateStatut('certifie')" :disabled="loading" class="px-2 py-1 text-xs bg-white border border-green-200 text-green-600 rounded hover:bg-green-50 disabled:opacity-50">Certifié</button>
                                                <button @click="updateStatut('annule')" :disabled="loading" class="px-2 py-1 text-xs bg-white border border-gray-200 text-gray-600 rounded hover:bg-gray-50 disabled:opacity-50">Annulé</button>
                                                
                                                <!-- Loader -->
                                                <svg x-show="loading" class="animate-spin h-4 w-4 text-indigo-500 absolute -left-6 top-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucun apprenant inscrit à cette session.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            <!-- TAB: EVALUATIONS -->
            <div x-show="tab === 'evaluations'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Évaluations des Apprenants</h3>
                    @can('evaluation-session-create')
                    <a href="{{ route('evaluations.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                        + Soumettre Évaluation
                    </a>
                    @endcan
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @forelse($session->evaluations as $evaluation)
                        <x-card class="flex flex-col h-full relative">
                            <div class="absolute top-4 right-4 flex gap-2">
                                <div class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-1 rounded-full border border-indigo-200">
                                    Pédagogie: {{ $evaluation->note_pedagogie }}/5
                                </div>
                                <div class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded-full border border-blue-200">
                                    Technique: {{ $evaluation->note_technique }}/5
                                </div>
                            </div>
                            
                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $evaluation->user->nom_complet }}</h4>
                            <p class="text-xs text-gray-500 mt-1">
                                A évalué le formateur : <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $evaluation->formateur->user->nom_complet }}</span>
                            </p>
                            
                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-800 rounded text-sm text-gray-700 dark:text-gray-300 italic flex-1 border border-gray-100 dark:border-gray-700">
                                "{!! nl2br(e($evaluation->avis_textuel ?? 'Aucun commentaire.')) !!}"
                            </div>
                        </x-card>
                    @empty
                        <div class="col-span-full py-8 text-center text-gray-500 bg-white dark:bg-gray-800 rounded-lg">Aucune évaluation soumise pour cette session.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- Script Alpine pour changement statut Inscription -->
    <script>
        function inscriptionStatus(id, initialStatut) {
            return {
                id: id,
                currentStatut: initialStatut,
                loading: false,
                
                updateStatut(newStatut) {
                    if (this.currentStatut === newStatut) return;
                    if (!confirm('Changer le statut vers ' + newStatut + ' ?')) return;
                    
                    this.loading = true;
                    
                    fetch(`/inscriptions/${this.id}/statut`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ statut_inscription: newStatut })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.loading = false;
                        if(data.success) {
                            this.currentStatut = data.statut_inscription;
                        } else {
                            alert(data.error || 'Erreur réseau.');
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                        alert('Une erreur est survenue lors de la mise à jour.');
                    });
                }
            }
        }
    </script>
</x-app-layout>
