<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Fiche Matériel : {{ $asset->marque }} {{ $asset->modele }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">S/N: {{ $asset->num_serie }}</p>
            </div>
            <div class="flex gap-2">
                @can('asset-edit')
                <a href="{{ route('asset_materiels.edit', $asset->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    Modifier Matériel
                </a>
                @endcan
                <a href="{{ route('asset_materiels.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'details' }" @hashchange.window="tab = window.location.hash.substring(1)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-card class="bg-indigo-50 border-indigo-100">
                    <p class="text-sm text-indigo-600 font-medium">Statut</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1 capitalize">{{ str_replace('_', ' ', $asset->statut_materiel) }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Type</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $asset->typeMateriel->libelle_type }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Valeur d'Achat</p>
                    <p class="text-2xl font-bold font-mono text-gray-900 mt-1">{{ number_format($asset->prix_achat, 2, ',', ' ') }} DHS</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Pannes Historiques</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $asset->ticketsMaintenance->count() }}</p>
                </x-card>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#details" :class="tab === 'details' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Caractéristiques</a>
                    <a href="#assignations" :class="tab === 'assignations' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Cycle des Assignations</a>
                    <a href="#maintenance" :class="tab === 'maintenance' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Registre Maintenance</a>
                </nav>
            </div>

            <!-- TAB: DETAILS -->
            <div x-show="tab === 'details'" x-cloak class="space-y-4">
                <x-card>
                    <x-slot name="header"><h3 class="text-lg font-medium text-gray-900">Spécifications</h3></x-slot>
                    <div class="grid grid-cols-2 gap-4">
                        <div><p class="text-sm text-gray-500">Marque</p><p class="font-medium text-gray-900">{{ $asset->marque }}</p></div>
                        <div><p class="text-sm text-gray-500">Modèle</p><p class="font-medium text-gray-900">{{ $asset->modele }}</p></div>
                        <div><p class="text-sm text-gray-500">N° de Série</p><p class="font-medium text-gray-900 font-mono">{{ $asset->num_serie }}</p></div>
                        <div><p class="text-sm text-gray-500">Date d'achat</p><p class="font-medium text-gray-900">{{ $asset->date_achat_actif ? $asset->date_achat_actif->format('d/m/Y') : 'Inconnue' }}</p></div>
                    </div>
                </x-card>
            </div>

            <!-- TAB: ASSIGNATIONS -->
            <div x-show="tab === 'assignations'" x-cloak class="space-y-4" x-data="assignationManager()">
                
                @if($asset->statut_materiel === 'disponible')
                    @can('manage-assets')
                    <x-card class="bg-indigo-50/50 border-indigo-100">
                        <form action="{{ route('assignation_materiels.store', $asset->id) }}" method="POST" class="flex items-end gap-4">
                            @csrf
                            <div class="flex-1">
                                <x-input-label for="user_id" value="Assigner à un collaborateur" />
                                <select name="user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach(\App\Models\User::all() as $u)
                                        <option value="{{ $u->id }}">{{ $u->nom_complet }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="date_remise" value="Date de remise" />
                                <x-text-input type="date" name="date_remise" value="{{ date('Y-m-d') }}" required class="mt-1" />
                            </div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-semibold">Attribuer</button>
                        </form>
                    </x-card>
                    @endcan
                @elseif($asset->statut_materiel === 'en_panne')
                    <div class="p-4 bg-red-50 text-red-600 rounded-lg border border-red-200">
                        Ce matériel est actuellement en réparation et ne peut être assigné.
                    </div>
                @endif

                <x-card>
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Utilisateur</th>
                                <th class="px-6 py-3">Date Remise</th>
                                <th class="px-6 py-3">Date Restitution</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asset->users()->orderByPivot('created_at', 'desc')->get() as $userAssign)
                                <tr class="bg-white border-b hover:bg-gray-50" id="row-assign-{{ $userAssign->pivot->id }}">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $userAssign->nom_complet }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($userAssign->pivot->date_remise)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4" id="restit-date-{{ $userAssign->pivot->id }}">
                                        @if($userAssign->pivot->date_restitution)
                                            {{ \Carbon\Carbon::parse($userAssign->pivot->date_restitution)->format('d/m/Y') }}
                                        @else
                                            <span class="text-indigo-600 font-medium">En cours d'utilisation</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if(!$userAssign->pivot->date_restitution)
                                            @can('manage-assets')
                                            <button @click="restituer({{ $userAssign->pivot->id }})" class="text-sm font-medium text-red-600 hover:text-red-900 border border-red-200 px-3 py-1 rounded hover:bg-red-50 transition">
                                                Restituer (Clôturer)
                                            </button>
                                            @endcan
                                        @else
                                            <span class="text-xs text-gray-400">Clôturé</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucun historique d'assignation.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-card>
            </div>

            <!-- TAB: MAINTENANCE -->
            <div x-show="tab === 'maintenance'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Historique des pannes</h3>
                    @can('ticket-create')
                    <a href="{{ route('ticket_maintenances.create') }}" class="text-xs font-medium text-indigo-600 hover:underline">
                        + Signaler un incident
                    </a>
                    @endcan
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                    @forelse($asset->ticketsMaintenance as $ticket)
                        <x-card>
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-gray-900">Ticket #{{ $ticket->id }}</h4>
                                <x-badge type="{{ $ticket->statut_ticket === 'resolu' ? 'success' : ($ticket->statut_ticket === 'en_atelier' ? 'warning' : 'danger') }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->statut_ticket)) }}
                                </x-badge>
                            </div>
                            <p class="text-sm text-gray-700 italic">"{{ $ticket->description_panne }}"</p>
                            <div class="mt-4 flex justify-between items-center text-xs text-gray-500 border-t border-gray-100 pt-2">
                                <span>Signalé par {{ $ticket->user->nom_complet }} le {{ $ticket->created_at->format('d/m/Y') }}</span>
                                <span class="font-mono text-gray-900 font-bold">Coût: {{ $ticket->cout_reparation }} DHS</span>
                            </div>
                        </x-card>
                    @empty
                        <div class="py-8 text-center text-gray-500 bg-white rounded-lg">Aucun ticket de maintenance enregistré.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script>
        function assignationManager() {
            return {
                restituer(pivotId) {
                    if(!confirm('Confirmer la restitution de ce matériel ? Cela clôturera son cycle d\'utilisation actuel.')) return;
                    
                    fetch(`/assignations/${pivotId}/restituer`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            // Update UI instead of full reload for smooth UX
                            document.getElementById(`restit-date-${pivotId}`).innerText = data.date_restitution;
                            alert(data.message);
                            window.location.reload(); // Reload pour mettre à jour les KPIs et les verrous
                        } else {
                            alert(data.message || 'Erreur lors de la restitution.');
                        }
                    }).catch(err => {
                        alert('Erreur réseau.');
                    });
                }
            }
        }
    </script>
</x-app-layout>
