<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Projet : {{ $projet->nom_projet }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Client : {{ $projet->client?->nom_societe ?? $projet->client?->user?->nom_complet ?? 'Inconnu' }}</p>
            </div>
            <div class="flex gap-2">
                @can('projet-edit')
                <a href="{{ route('projets.edit', $projet->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Modifier Projet
                </a>
                @endcan
                <a href="{{ route('projets.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <!-- CDN SortableJS pour le Kanban -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <div class="py-12" x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'kanban' }" @hashchange.window="tab = window.location.hash.substring(1)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPI et Résumé -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-card class="bg-indigo-50 dark:bg-indigo-900/20 border-indigo-100 dark:border-indigo-800">
                    <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">Statut</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1 capitalize">{{ $projet->statut_projet }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Budget Vendu</p>
                    <p class="text-2xl font-bold font-mono text-gray-900 dark:text-white mt-1">{{ number_format($projet->budget_vendu, 2, ',', ' ') }} DHS</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Heures Saisies</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalHeures }} h</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Livrables</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $projet->livrables?->count() ?? 0 }}</p>
                </x-card>
            </div>

            <!-- Liste des Technologies -->
            @if($projet->technologies?->isNotEmpty() ?? false)
            <div class="flex flex-wrap gap-2 mt-4">
                <span class="text-sm text-gray-500 font-medium self-center mr-2">Technologies :</span>
                @foreach($projet->technologies as $tech)
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded shadow-sm border border-gray-200">
                        {{ $tech->nom_tech }} (v{{ $tech->version }})
                    </span>
                @endforeach
            </div>
            @endif

            <!-- Navigation Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#kanban" :class="tab === 'kanban' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Kanban des Tâches</a>
                    <a href="#livrables" :class="tab === 'livrables' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Livrables & Jalons</a>
                    @unless(auth()->user()->hasRole('Client'))
                    <a href="#timesheets" :class="tab === 'timesheets' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Feuilles de Temps</a>
                    @endunless
                </nav>
            </div>

            <!-- Contenu des Tabs -->
            
            <!-- TAB: KANBAN -->
            <div x-show="tab === 'kanban'" x-cloak class="space-y-4">
                <div class="flex justify-end">
                    @can('tache-create')
                    <a href="{{ route('projets.taches.create', $projet->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                        + Nouvelle Tâche
                    </a>
                    @endcan
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4" x-data="kanbanBoard()">
                    @php
                        $colonnes = [
                            'backlog' => ['label' => 'Backlog', 'color' => 'gray'],
                            'en_cours' => ['label' => 'En Cours', 'color' => 'blue'],
                            'en_revue' => ['label' => 'En Revue', 'color' => 'yellow'],
                            'termine' => ['label' => 'Terminé', 'color' => 'green']
                        ];
                    @endphp

                    @foreach($colonnes as $statut => $config)
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4 flex flex-col h-full border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-gray-700 dark:text-gray-300 uppercase text-xs tracking-wider">{{ $config['label'] }}</h3>
                            <span class="bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400 text-xs px-2 py-0.5 rounded-full">{{ count($tachesParStatut[$statut]) }}</span>
                        </div>
                        
                        <!-- Conteneur Sortable -->
                        <div class="kanban-col flex-1 space-y-3 min-h-[300px]" data-statut="{{ $statut }}">
                            @foreach($tachesParStatut[$statut] as $tache)
                                <div class="kanban-card bg-white dark:bg-gray-700 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 cursor-grab active:cursor-grabbing hover:shadow-md transition" data-id="{{ $tache->id }}">
                                    <p class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-2">{{ $tache->titre_tache }}</p>
                                    <div class="flex justify-between items-center mt-2">
                                        @php
                                            $prioColor = match($tache->pivot?->priorite ?? 'moyenne') {
                                                'basse' => 'gray',
                                                'moyenne' => 'info',
                                                'haute' => 'warning',
                                                'bloquante' => 'danger',
                                                default => 'gray'
                                            };
                                        @endphp
                                        <x-badge :type="$prioColor" class="text-[10px] uppercase">{{ $tache->pivot?->priorite ?? 'N/A' }}</x-badge>
                                        <div class="text-xs text-gray-400 flex gap-1">
                                            @can('tache-edit')
                                            <a href="{{ route('projets.taches.edit', [$projet->id, $tache->id]) }}" class="hover:text-indigo-500" title="Éditer">✎</a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- TAB: LIVRABLES -->
            <div x-show="tab === 'livrables'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Jalons & Documents</h3>
                    @can('livrable-create')
                    <a href="{{ route('projets.livrables.create', $projet->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                        + Nouveau Livrable
                    </a>
                    @endcan
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($projet->livrables as $livrable)
                        <x-card>
                            <div class="flex justify-between items-start">
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $livrable->titre_jalon }}</h4>
                                @php
                                    $lColor = match($livrable->statut_client) {
                                        'en_attente' => 'warning',
                                        'valide' => 'success',
                                        'rejete_avec_corrections' => 'danger',
                                        default => 'gray'
                                    };
                                @endphp
                                <x-badge :type="$lColor">{{ str_replace('_', ' ', $livrable->statut_client) }}</x-badge>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Limite : {{ $livrable->date_limite_soumission?->format('d/m/Y') ?? 'N/A' }}</p>
                            
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                @if($livrable->fichier_path)
                                    <a href="{{ route('livrables.download', $livrable->id) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        {{ Str::limit($livrable->fichier_nom_original, 20) }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400 italic">Aucun fichier</span>
                                @endif
                                
                                @can('livrable-edit')
                                <a href="{{ route('livrables.edit', $livrable->id) }}" class="text-xs text-gray-500 hover:text-gray-900 dark:hover:text-white">Modifier</a>
                                @endcan
                            </div>
                        </x-card>
                    @empty
                        <div class="col-span-full py-8 text-center text-gray-500 bg-white dark:bg-gray-800 rounded-lg">Aucun livrable défini.</div>
                    @endforelse
                </div>
            </div>

            @unless(auth()->user()->hasRole('Client'))
            <!-- TAB: TIMESHEETS -->
            <div x-show="tab === 'timesheets'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Heures Saisies</h3>
                    @can('feuille-temps-create')
                    <a href="{{ route('projets.feuille_temps.create', $projet->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                        + Saisir des heures
                    </a>
                    @endcan
                </div>

                <x-card>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Employé</th>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3">Durée</th>
                                    <th scope="col" class="px-6 py-3">Tâches Associées</th>
                                    <th scope="col" class="px-6 py-3">Commentaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projet->feuilleTemps as $ft)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ $ft->employe?->user?->nom_complet ?? 'Inconnu' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($ft->date_effort)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 font-bold text-indigo-600 dark:text-indigo-400">
                                            {{ $ft->duree_heures }} h
                                        </td>
                                        <td class="px-6 py-4">
                                            @foreach($ft->taches as $t)
                                                <span class="block text-xs text-gray-500">• {{ $t->titre_tache }}</span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 text-xs italic text-gray-500 max-w-xs truncate">
                                            {{ $ft->commentaire ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucune heure saisie.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
            @endunless

        </div>
    </div>

    <!-- Logique Alpine / SortableJS pour Kanban -->
    <script>
        function kanbanBoard() {
            return {
                init() {
                    const columns = document.querySelectorAll('.kanban-col');
                    columns.forEach(col => {
                        new Sortable(col, {
                            group: 'shared',
                            animation: 150,
                            ghostClass: 'opacity-50',
                            onEnd: (evt) => {
                                const itemEl = evt.item;
                                const toList = evt.to;
                                
                                const tacheId = itemEl.dataset.id;
                                const newStatut = toList.dataset.statut;
                                
                                if (evt.from === toList) return;
                                
                                fetch(`/projets/{{ $projet->id }}/taches/${tacheId}/statut`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ statut_tache: newStatut })
                                })
                                .then(res => {
                                    if (!res.ok) throw new Error('Network error');
                                    // Update counter logic could go here
                                })
                                .catch(err => {
                                    alert("Erreur lors de la mise à jour du statut. La page va se recharger.");
                                    window.location.reload();
                                });
                            }
                        });
                    });
                }
            }
        }
    </script>
</x-app-layout>
