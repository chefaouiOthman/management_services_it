<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Zones de Sécurité') }}
            </h2>
            @can('zone-create')
            <a href="{{ route('zones.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Ajouter une Zone
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($zones->isEmpty())
                <x-card>
                    <p class="text-center text-gray-500 py-4">Aucune zone configurée.</p>
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($zones as $zone)
                        <x-card>
                            <x-slot name="header">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $zone->nom_salle }}</h3>
                                    @if($zone->est_active)
                                        <x-badge type="success">Active</x-badge>
                                    @else
                                        <x-badge type="danger">Désactivée</x-badge>
                                    @endif
                                </div>
                            </x-slot>

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Code Zone</span>
                                    <span class="font-mono font-medium text-gray-900 dark:text-gray-100">{{ $zone->code_zone }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Niveau requis</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">Niv. {{ $zone->niveau_requis }}</span>
                                </div>
                            </div>

                            <x-slot name="footer">
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('zones.show', $zone->id) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        Voir le journal →
                                    </a>
                                    <div class="flex space-x-2">
                                        @can('zone-edit')
                                        <a href="{{ route('zones.edit', $zone->id) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Modifier</a>
                                        @endcan
                                        @can('zone-delete')
                                        <form action="{{ route('zones.destroy', $zone->id) }}" method="POST" onsubmit="return confirm('Supprimer cette zone ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Supprimer</button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </x-slot>
                        </x-card>
                    @endforeach
                </div>
            @endif

            <!-- Section Historique des Passages (Master Table Admin) -->
            <div class="mt-12">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Historique des Passages (Global)
                    </h2>
                    @can('zone-create')
                    <!-- Bouton pour créer un passage manuel -->
                    <div x-data="{ openLogModal: false }">
                        <button @click="openLogModal = true" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-semibold hover:bg-gray-700">
                            + Journaliser manuellement
                        </button>
                        
                        <!-- Modale Création Manuelle -->
                        <div x-show="openLogModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" @click.self="openLogModal = false">
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-lg mx-auto shadow-2xl">
                                <h3 class="text-lg font-bold mb-4">Créer une entrée d'accès</h3>
                                <form action="{{ route('historique_passages.store') }}" method="POST" class="space-y-4" x-data="{ useCurrentTime: true }">
                                    @csrf
                                    <div>
                                        <x-input-label value="Utilisateur" />
                                        <select name="user_id" required class="mt-1 w-full border-gray-300 rounded-md">
                                            @foreach(\App\Models\User::all() as $u)
                                                <option value="{{ $u->id }}">{{ $u->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label value="Zone" />
                                        <select name="zone_id" required class="mt-1 w-full border-gray-300 rounded-md">
                                            @foreach(\App\Models\Zone::all() as $z)
                                                <option value="{{ $z->id }}">{{ $z->nom_salle }} ({{ $z->code_zone }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <x-input-label value="Horodatage" />
                                            <label class="text-xs flex items-center text-gray-500">
                                                <input type="checkbox" x-model="useCurrentTime" class="mr-1 rounded"> Heure actuelle
                                            </label>
                                        </div>
                                        <input type="datetime-local" name="horodatage" class="w-full border-gray-300 rounded-md" x-bind:disabled="useCurrentTime" :value="useCurrentTime ? '' : '{{ now()->format('Y-m-d\TH:i') }}'">
                                    </div>
                                    <div>
                                        <x-input-label value="Statut" />
                                        <select name="tentative_statut" required class="mt-1 w-full border-gray-300 rounded-md">
                                            <option value="autorise">Autorisé</option>
                                            <option value="refuse_niveau_insuffisant">Refusé (Niveau insuffisant)</option>
                                            <option value="refuse_zone_desactivee">Refusé (Zone désactivée)</option>
                                        </select>
                                    </div>
                                    <div class="flex justify-end gap-3 mt-6">
                                        <button type="button" @click="openLogModal = false" class="px-4 py-2 bg-gray-100 rounded-md">Annuler</button>
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>

                <x-card>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">Date/Heure</th>
                                    <th class="px-6 py-3">Utilisateur</th>
                                    <th class="px-6 py-3">Zone</th>
                                    <th class="px-6 py-3">Statut actuel</th>
                                    <th class="px-6 py-3 text-right">Modération Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $logs = \App\Models\HistoriquePassage::with(['user', 'zone'])->latest()->paginate(25);
                                @endphp
                                @forelse($logs as $log)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium">{{ \Carbon\Carbon::parse($log->horodatage)->format('d/m/Y H:i:s') }}</td>
                                        <td class="px-6 py-4">{{ $log->user->nom_complet ?? 'Inconnu' }}</td>
                                        <td class="px-6 py-4">{{ $log->zone->nom_salle ?? 'Zone inconnue' }}</td>
                                        <td class="px-6 py-4">
                                            @if($log->tentative_statut == 'autorise')
                                                <x-badge type="success">Autorisé</x-badge>
                                            @elseif($log->tentative_statut == 'refuse_niveau_insuffisant')
                                                <x-badge type="warning">Niveau Insuffisant</x-badge>
                                            @elseif($log->tentative_statut == 'refuse_zone_desactivee')
                                                <x-badge type="danger">Zone Désactivée</x-badge>
                                            @else
                                                <x-badge type="gray">En attente / {{ $log->tentative_statut }}</x-badge>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @can('zone-edit')
                                            <!-- Moderation Dropdown Form -->
                                            <form action="{{ route('historique_passages.update', $log->id) }}" method="POST" class="inline-flex items-center space-x-2">
                                                @csrf
                                                @method('PUT')
                                                <select name="tentative_statut" onchange="this.form.submit()" class="text-xs border-gray-300 rounded-md py-1 px-2 focus:ring-indigo-500">
                                                    <option value="" disabled selected>Modifier...</option>
                                                    <option value="autorise">Approuver</option>
                                                    <option value="refuse_niveau_insuffisant">Refuser (Niv.)</option>
                                                    <option value="refuse_zone_desactivee">Refuser (Zone Off)</option>
                                                </select>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucun historique d'accès disponible.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4 pb-4">
                        {{ $logs->links() }}
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
