<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Helpdesk : Maintenance IT
            </h2>
            @can('ticket-create')
            <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Signaler une Panne
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <x-card class="border-l-4 border-red-500">
                    <p class="text-sm text-gray-500 font-medium">Nouveaux Signalements</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tickets->where('statut_ticket', 'signale')->count() }}</p>
                </x-card>
                <x-card class="border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500 font-medium">En cours de réparation</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tickets->where('statut_ticket', 'en_atelier')->count() }}</p>
                </x-card>
                <x-card class="border-l-4 border-green-500">
                    <p class="text-sm text-gray-500 font-medium">Tickets Résolus</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tickets->where('statut_ticket', 'resolu')->count() }}</p>
                </x-card>
            </div>

            <x-card class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg text-gray-800">Matériels IT / Assets</h3>
                    <p class="text-sm text-gray-500">Sélectionnez un matériel pour signaler un incident</p>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0">
                            <tr>
                                <th scope="col" class="px-6 py-3">Matériel</th>
                                <th scope="col" class="px-6 py-3">Numéro de Série</th>
                                <th scope="col" class="px-6 py-3">Statut</th>
                                <th scope="col" class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                                <tr class="bg-white border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 font-medium text-gray-900">
                                        {{ $asset->marque }} {{ $asset->modele }}<br>
                                        <span class="text-xs text-gray-500">{{ $asset->typeMateriel->libelle_type ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-3">{{ $asset->num_serie }}</td>
                                    <td class="px-6 py-3">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full border uppercase 
                                            {{ $asset->statut_materiel === 'disponible' ? 'bg-green-100 text-green-700 border-green-200' : '' }}
                                            {{ $asset->statut_materiel === 'attribue' ? 'bg-blue-100 text-blue-700 border-blue-200' : '' }}
                                            {{ $asset->statut_materiel === 'en_panne' ? 'bg-red-100 text-red-700 border-red-200' : '' }}
                                            {{ $asset->statut_materiel === 'reforme' ? 'bg-gray-100 text-gray-700 border-gray-200' : '' }}">
                                            {{ str_replace('_', ' ', $asset->statut_materiel) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        @can('ticket-create')
                                        <a href="{{ route('tickets.create', ['asset_id' => $asset->id]) }}" class="inline-flex items-center px-3 py-1 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 border border-red-200 rounded text-xs font-semibold transition">
                                            + Signaler un incident
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucun matériel disponible.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <h3 class="font-bold text-lg text-gray-800 mb-2 mt-8">Historique des Tickets</h3>
            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID / Date</th>
                                <th scope="col" class="px-6 py-3">Demandeur</th>
                                <th scope="col" class="px-6 py-3">Matériel Concerne</th>
                                <th scope="col" class="px-6 py-3">Statut (Action Rapide)</th>
                                <th scope="col" class="px-6 py-3 text-right">Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets->sortByDesc('created_at') as $ticket)
                                <tr class="bg-white border-b hover:bg-gray-50 transition" x-data="ticketManager({{ $ticket->id }}, '{{ $ticket->statut_ticket }}')">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-900">#{{ $ticket->id }}</span><br>
                                        <span class="text-xs text-gray-400">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $ticket->user->nom_complet }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('asset_materiels.show', $ticket->asset_materiel_id) }}" class="text-indigo-600 hover:underline">
                                            {{ $ticket->assetMateriel->marque }} {{ $ticket->assetMateriel->modele }}
                                        </a><br>
                                        <span class="text-xs text-gray-500">SN: {{ $ticket->assetMateriel->num_serie }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 text-xs font-bold rounded-full border uppercase w-24 text-center"
                                                  :class="{
                                                      'bg-red-100 text-red-700 border-red-200': currentStatut === 'signale',
                                                      'bg-yellow-100 text-yellow-700 border-yellow-200': currentStatut === 'en_atelier',
                                                      'bg-green-100 text-green-700 border-green-200': currentStatut === 'resolu'
                                                  }"
                                                  x-text="currentStatut.replace('_', ' ')">
                                            </span>
                                            
                                            @can('manage-assets')
                                            <div class="flex flex-col gap-1">
                                                <button x-show="currentStatut === 'signale'" @click="updateStatut('en_atelier')" class="text-[10px] bg-yellow-50 text-yellow-700 border border-yellow-200 px-2 rounded hover:bg-yellow-100">→ Atelier</button>
                                                <button x-show="currentStatut === 'en_atelier'" @click="updateStatut('resolu')" class="text-[10px] bg-green-50 text-green-700 border border-green-200 px-2 rounded hover:bg-green-100">→ Résoudre</button>
                                            </div>
                                            @endcan
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('ticket_maintenances.show', $ticket->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Voir</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucun ticket de maintenance.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function ticketManager(id, initialStatut) {
            return {
                id: id,
                currentStatut: initialStatut,
                
                updateStatut(newStatut) {
                    if(!confirm('Passer le ticket au statut : ' + newStatut.replace('_', ' ') + ' ?')) return;
                    
                    fetch(`/tickets/${this.id}/statut`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ statut_ticket: newStatut })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.currentStatut = data.statut_ticket;
                        } else {
                            alert(data.error || 'Erreur.');
                        }
                    })
                    .catch(err => {
                        alert('Erreur réseau.');
                    });
                }
            }
        }
    </script>
</x-app-layout>
