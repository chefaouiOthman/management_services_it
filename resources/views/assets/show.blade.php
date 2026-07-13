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
                <a href="{{ route('assets.edit', $asset->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    Modifier Matériel
                </a>
                @endcan
                <a href="{{ route('assets.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'details' }" @hashchange.window="tab = window.location.hash.substring(1)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tickets->count() }}</p>
                </x-card>
            </div>

            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#details"       @click="tab='details'"       :class="tab === 'details'       ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition cursor-pointer">Caractéristiques</a>
                    <a href="#assignations"  @click="tab='assignations'"  :class="tab === 'assignations'  ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition cursor-pointer">Cycle des Assignations</a>
                    <a href="#maintenance"   @click="tab='maintenance'"   :class="tab === 'maintenance'   ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition cursor-pointer">Registre Maintenance</a>
                </nav>
            </div>

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

            <div x-show="tab === 'assignations'" x-cloak class="space-y-4" x-data="assignationManager()">

                @can('manage-assets')
                <x-card class="bg-indigo-50/50 border-indigo-100">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-semibold text-gray-800">Ajouter une assignation</h4>
                    </div>
                    @if(!in_array($asset->statut_materiel, ['disponible', 'attribue']))
                    <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
                        <strong>Attention :</strong> Ce matériel est en statut <strong>{{ str_replace('_', ' ', $asset->statut_materiel) }}</strong>.
                        L'assignation est bloquée. Résolvez d'abord le problème lié à son statut.
                    </div>
                    @endif
                    @if(in_array($asset->statut_materiel, ['disponible', 'attribue']))
                    <form action="{{ route('assignation_materiels.store', $asset->id) }}" method="POST" class="flex items-end gap-4 flex-wrap">
                        @csrf
                        <input type="hidden" name="asset_materiel_id" value="{{ $asset->id }}">

                        <div class="flex-1 min-w-48">
                            <x-input-label for="user_id" value="Assigner à un collaborateur" />
                            <select name="user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500">
                                <option value="">-- Sélectionner --</option>
                                @foreach(\App\Models\User::orderBy('nom_complet')->get() as $u)
                                    <option value="{{ $u->id }}">{{ $u->nom_complet }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-1" :messages="$errors->get('user_id')" />
                        </div>
                        <div>
                            <x-input-label for="date_remise" value="Date de remise" />
                            <x-text-input type="date" name="date_remise" value="{{ date('Y-m-d') }}" required class="mt-1" />
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-semibold">Attribuer</button>
                    </form>
                    @if($asset->statut_materiel === 'attribue')
                    <p class="mt-2 text-xs text-amber-600">Ce matériel est déjà attribué. En sauvegardant, l'assignation active sera automatiquement clôturée.</p>
                    @endif
                    @endif
                </x-card>
                @endcan

                <x-card>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Utilisateur</th>
                                    <th class="px-6 py-3">Date Remise</th>
                                    <th class="px-6 py-3">Date Restitution</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </table>
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
                                            <div class="flex justify-end items-center gap-3">
                                                @if(!$userAssign->pivot->date_restitution)
                                                    @can('manage-assets')
                                                    <button data-restituer="{{ $userAssign->pivot->id }}" @click="restituer({{ $userAssign->pivot->id }})" class="text-sm font-medium text-red-600 hover:text-red-900 border border-red-200 px-3 py-1 rounded hover:bg-red-50 transition">
                                                        Restituer
                                                    </button>
                                                    @endcan
                                                @else
                                                    <span class="text-xs text-gray-400">Clôturé</span>
                                                @endif
                                                @can('manage-assets')
                                                <form action="{{ route('assignation_materiels.destroy', $userAssign->pivot->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette assignation ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-500 hover:text-red-800">Supprimer</button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucun historique d'assignation.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            <div x-show="tab === 'maintenance'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Registre des Pannes & Incidents</h3>
                    @can('ticket-create')
                    <a href="{{ route('tickets.create', ['asset_id' => $asset->id]) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-red-700 transition">
                        + Signaler un incident
                    </a>
                    @endcan
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @forelse($tickets as $ticket)
                        <x-card>
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="font-bold text-gray-900">Ticket #{{ $ticket->id }}</h4>
                                        <x-badge type="{{ $ticket->statut_ticket === 'resolu' ? 'success' : ($ticket->statut_ticket === 'en_atelier' ? 'warning' : 'danger') }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->statut_ticket)) }}
                                        </x-badge>
                                    </div>
                                    <p class="text-sm text-gray-700 italic">"{{ $ticket->description_panne }}"</p>
                                    <div class="mt-3 flex justify-between items-center text-xs text-gray-500 border-t border-gray-100 pt-2">
                                        <span>Signalé par <strong>{{ optional($ticket->user)->nom_complet ?? 'N/A' }}</strong> le {{ $ticket->created_at->format('d/m/Y') }}</span>
                                        <span class="font-mono text-gray-900 font-bold">Coût: {{ number_format($ticket->cout_reparation, 2, ',', ' ') }} DHS</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2 ml-4">
                                    @can('ticket-edit')
                                    <a href="{{ route('tickets.edit', $ticket->id) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-900 border border-indigo-200 px-2 py-1 rounded hover:bg-indigo-50 transition">Modifier</a>
                                    @endcan
                                    @can('ticket-delete')
                                    <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Supprimer ce ticket de maintenance ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-900 border border-red-200 px-2 py-1 rounded hover:bg-red-50 transition">Supprimer</button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </x-card>
                    @empty
                        <div class="py-8 text-center text-gray-500 bg-white rounded-lg border border-gray-100">Aucun ticket de maintenance enregistré.</div>
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
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if(data.success) {
                            var dateCell = document.getElementById('restit-date-' + pivotId);
                            if (dateCell) {
                                dateCell.textContent = data.date_restitution;
                            }

                            var btn = document.querySelector('[data-restituer="' + pivotId + '"]');
                            if (btn) {
                                btn.outerHTML = '<span class="text-xs text-gray-400">Clôturé</span>';
                            }

                            window.location.reload();
                        } else {
                            alert(data.message || 'Erreur lors de la restitution.');
                        }
                    }).catch(function(err) {
                        alert('Erreur réseau.');
                    });
                }
            }
        }
    </script>
</x-app-layout>