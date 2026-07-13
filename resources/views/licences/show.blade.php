<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Détails de la Licence : {{ $licence->nom_logiciel }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Clé: {{ $licence->cle_licence }}</p>
            </div>
            <div class="flex gap-2">
                @can('licence-edit')
                <a href="{{ route('licences.edit', $licence->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    Modifier Licence
                </a>
                @endcan
                <a href="{{ route('licences.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $expDate = \Carbon\Carbon::parse($licence->date_expiration);
                    $isExpired = $expDate->isPast();
                    $activeAssignments = $licence->users()->wherePivotNull('date_revocation')->count();
                    $totalAssignments = $licence->users()->count();
                @endphp
                <x-card class="bg-{{ $isExpired ? 'red' : 'green' }}-50 border-{{ $isExpired ? 'red' : 'green' }}-100">
                    <p class="text-sm text-{{ $isExpired ? 'red' : 'green' }}-600 font-medium">Statut</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $isExpired ? 'Expirée' : 'Valide' }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Attributions Actives</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $activeAssignments }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Total Historique</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalAssignments }}</p>
                </x-card>
            </div>

            <!-- Licence Details -->
            <x-card>
                <x-slot name="header"><h3 class="text-lg font-medium text-gray-900">Informations de la Licence</h3></x-slot>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Nom du Logiciel</p>
                        <p class="font-medium text-gray-900">{{ $licence->nom_logiciel }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Clé de Licence</p>
                        <p class="font-medium text-gray-900 font-mono text-sm bg-gray-100 p-2 rounded">{{ $licence->cle_licence }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date d'Expiration</p>
                        <p class="font-medium text-gray-900">{{ $expDate->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jours Restants</p>
                        <p class="font-medium text-gray-900 {{ $isExpired ? 'text-red-600' : 'text-green-600' }}">
                            {{ $isExpired ? 'Expiré depuis ' . $expDate->diffForHumans() : $expDate->diffInDays() . ' jours' }}
                        </p>
                    </div>
                </div>
            </x-card>

            <!-- Active Assignments -->
            <x-card x-data="licenceManager()">
                <x-slot name="header">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Attributions Actives</h3>
                        @can('manage-assets')
                        <button @click="openAssignForm = !openAssignForm" class="text-xs bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded border border-indigo-200 hover:bg-indigo-100 transition">+ Attribuer</button>
                        @endcan
                    </div>
                </x-slot>

                <div x-show="openAssignForm" x-collapse class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <form action="{{ route('assignation_licences.store', $licence->id) }}" method="POST" class="flex flex-col md:flex-row gap-4">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateur</label>
                            <select name="user_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500">
                                <option value="">Sélectionner un utilisateur...</option>
                                @foreach(\App\Models\User::all() as $u)
                                    <option value="{{ $u->id }}">{{ $u->nom_complet }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date d'Attribution</label>
                            <input type="date" name="date_attribution" value="{{ date('Y-m-d') }}" required class="block border-gray-300 rounded-md shadow-sm focus:border-indigo-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded shadow-sm hover:bg-indigo-700 font-medium">Valider</button>
                        </div>
                    </form>
                </div>

                <div class="space-y-3">
                    @forelse($licence->users()->wherePivotNull('date_revocation')->get() as $user)
                        <div class="flex justify-between items-center p-4 bg-blue-50 border border-blue-100 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->nom_complet }}</p>
                                <p class="text-sm text-gray-500">Depuis le {{ \Carbon\Carbon::parse($user->pivot->date_attribution)->format('d/m/Y') }}</p>
                            </div>
                            @can('manage-assets')
                            <button @click="revoquer({{ $user->pivot->id }})" class="text-sm text-red-600 hover:text-red-900 bg-white border border-red-200 px-3 py-1.5 rounded shadow-sm hover:bg-red-50">
                                Révoquer
                            </button>
                            @endcan
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic py-4">Aucune attribution en cours.</p>
                    @endforelse
                </div>
            </x-card>

            <!-- Assignment History -->
            <x-card>
                <x-slot name="header"><h3 class="text-lg font-medium text-gray-900">Historique des Attributions</h3></x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Utilisateur</th>
                                <th class="px-6 py-3">Date Attribution</th>
                                <th class="px-6 py-3">Date Révocation</th>
                                <th class="px-6 py-3">Durée</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($licence->users()->orderByPivot('created_at', 'desc')->get() as $user)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $user->nom_complet }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($user->pivot->date_attribution)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">
                                        @if($user->pivot->date_revocation)
                                            {{ \Carbon\Carbon::parse($user->pivot->date_revocation)->format('d/m/Y') }}
                                        @else
                                            <span class="text-indigo-600 font-medium">En cours</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->pivot->date_revocation)
                                            {{ \Carbon\Carbon::parse($user->pivot->date_attribution)->diffInDays(\Carbon\Carbon::parse($user->pivot->date_revocation)) }} jours
                                        @else
                                            {{ \Carbon\Carbon::parse($user->pivot->date_attribution)->diffInDays(now()) }} jours
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucun historique d'attribution.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

        </div>
    </div>

    <script>
        function licenceManager() {
            return {
                openAssignForm: false,
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
