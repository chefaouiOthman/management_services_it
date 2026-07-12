<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gestion des Licences Logicielles
            </h2>
            @can('licence-create')
            <a href="{{ route('licences.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Nouvelle Licence
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 gap-6">
                @forelse($licences as $licence)
                    <x-card class="relative overflow-hidden" x-data="licenceManager()">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $licence->nom_logiciel }}</h3>
                                    <p class="text-sm text-gray-500 font-mono mt-1">Clé: {{ $licence->cle_licence }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @php
                                    $expDate = \Carbon\Carbon::parse($licence->date_expiration);
                                    $isExpired = $expDate->isPast();
                                @endphp
                                <x-badge type="{{ $isExpired ? 'danger' : 'success' }}">
                                    {{ $isExpired ? 'Expirée' : 'Valide' }}
                                </x-badge>
                                <p class="text-sm font-medium {{ $isExpired ? 'text-red-500' : 'text-gray-500' }} mt-1">Expire le {{ $expDate->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mb-4">
                            <button @click="openUsers = !openUsers" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="{'rotate-180': openUsers}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                Voir les utilisateurs
                            </button>
                        </div>

                        <div x-show="openUsers" x-collapse class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-100 pt-6 mt-2">
                            <!-- Assignation en cours -->
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-4 flex justify-between items-center">
                                    Attributions Actives
                                    @can('manage-assets')
                                    <button @click="openAssignForm = !openAssignForm" class="text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded border border-indigo-200 hover:bg-indigo-100 transition">+ Attribuer</button>
                                    @endcan
                                </h4>
                                
                                <div x-show="openAssignForm" x-collapse class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <form action="{{ route('assignation_licences.store', $licence->id) }}" method="POST" class="flex flex-col gap-3">
                                        @csrf
                                        <select name="user_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm">
                                            <option value="">Sélectionner un utilisateur...</option>
                                            @foreach(\App\Models\User::all() as $u)
                                                <option value="{{ $u->id }}">{{ $u->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                        <div class="flex gap-2">
                                            <x-text-input type="date" name="date_attribution" value="{{ date('Y-m-d') }}" required class="flex-1 text-sm" />
                                            <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded shadow-sm text-sm hover:bg-indigo-700 font-medium">Valider</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="space-y-3">
                                    @php
                                        $actives = $licence->users()->wherePivotNull('date_revocation')->get();
                                    @endphp
                                    @forelse($actives as $actif)
                                        <div class="flex justify-between items-center p-3 bg-blue-50 border border-blue-100 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">{{ $actif->nom_complet }}</p>
                                                <p class="text-xs text-gray-500">Depuis le {{ \Carbon\Carbon::parse($actif->pivot->date_attribution)->format('d/m/Y') }}</p>
                                            </div>
                                            @can('manage-assets')
                                            <button @click="revoquer({{ $actif->pivot->id }})" class="text-xs text-red-600 hover:text-red-900 bg-white border border-red-200 px-2 py-1 rounded shadow-sm hover:bg-red-50">
                                                Révoquer
                                            </button>
                                            @endcan
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 italic">Aucune attribution en cours.</p>
                                    @endforelse
                                </div>
                            </div>
                            
                            <!-- Historique -->
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-4">Historique des révocations</h4>
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                    @php
                                        $historique = $licence->users()->wherePivotNotNull('date_revocation')->orderByPivot('date_revocation', 'desc')->get();
                                    @endphp
                                    @forelse($historique as $hist)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                            <span class="text-sm font-medium text-gray-700">{{ $hist->nom_complet }}</span>
                                            <span class="text-xs text-gray-500">Fin : {{ \Carbon\Carbon::parse($hist->pivot->date_revocation)->format('d/m/Y') }}</span>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 italic">Aucun historique disponible.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="absolute top-4 right-4 flex items-center gap-3">
                            @can('licence-edit')
                            <a href="{{ route('licences.edit', $licence->id) }}" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">Modifier</a>
                            @endcan
                            @can('licence-delete')
                            <form action="{{ route('licences.destroy', $licence->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette licence ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-900 font-medium">Supprimer</button>
                            </form>
                            @endcan
                        </div>
                    </x-card>
                @empty
                    <x-card>
                        <p class="text-center text-gray-500 py-8">Aucune licence enregistrée.</p>
                    </x-card>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function licenceManager() {
            return {
                openAssignForm: false,
                openUsers: false,
                revoquer(pivotId) {
                    if(!confirm('Êtes-vous sûr de vouloir révoquer cette licence pour cet utilisateur ?')) return;
                    
                    fetch(`/assignations-licences/${pivotId}/revoquer`, {
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
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message || 'Erreur lors de la révocation.');
                        }
                    }).catch(err => {
                        alert('Erreur réseau.');
                    });
                }
            }
        }
    </script>
</x-app-layout>
