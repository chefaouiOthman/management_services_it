<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Zones de Sécurité') }}
            </h2>
            @can('zone-create')
            @if(auth()->user()->hasRole('Admin'))
            <a href="{{ route('zones.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Ajouter une Zone
            </a>
            @endif
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-search-filters :search="request('search')" searchPlaceholder="Rechercher par nom de zone..." :filters="[]" />

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
                                    @if(auth()->user()->hasRole('Admin'))
                                    <a href="{{ route('zones.show', $zone->id) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        Voir le journal →
                                    </a>
                                    @else
                                    <span></span>
                                    @endif
                                    @if(auth()->user()->hasRole('Admin'))
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
                                    @endif
                                </div>
                            </x-slot>
                        </x-card>
                    @endforeach
                </div>
            @endif

            <!-- Section Historique des Passages -->
            <div class="mt-12">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ auth()->user()->hasRole('Admin') ? 'Historique des Passages (Global)' : 'Mon Historique de Passages' }}
                    </h2>
                    @can('zone-create')
                    <div x-data="{ openLogModal: false }">
                        <button @click="openLogModal = true" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-semibold hover:bg-gray-700">
                            + Journaliser manuellement
                        </button>

                        <div x-show="openLogModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" @click.self="openLogModal = false">
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-lg mx-auto shadow-2xl">
                                <h3 class="text-lg font-bold mb-4">Créer une entrée d'accès</h3>
                                <form action="{{ route('historique_passages.store') }}" method="POST" class="space-y-4" x-data="{ useCurrentTime: true }">
                                    @csrf
                                    <div>
                                        <x-input-label value="Utilisateur" />
                                        <select name="user_id" required
                                            class="mt-1 w-full border-gray-300 rounded-md"
                                            @if(!auth()->user()->hasRole('Admin')) disabled @endif>
                                            @if(auth()->user()->hasRole('Admin'))
                                                @foreach(\App\Models\User::when(!auth()->user()->hasRole('Super Admin'), fn($q) => $q->whereDoesntHave('roles', fn($r) => $r->where('name', 'Super Admin')))->get() as $u)
                                                    <option value="{{ $u->id }}">{{ $u->nom_complet }}</option>
                                                @endforeach
                                            @else
                                                <option value="{{ auth()->id() }}" selected>{{ auth()->user()->nom_complet }}</option>
                                            @endif
                                        </select>
                                        @if(!auth()->user()->hasRole('Admin'))
                                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                        @endif
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
                                        <input type="text" name="horodatage" class="w-full border-gray-300 rounded-md" placeholder="Format: JJ/MM/AAAA HH:MM" x-bind:disabled="useCurrentTime" :value="useCurrentTime ? '' : '{{ now()->format('d/m/Y H:i') }}'">
                                        <p class="text-xs text-gray-500 mt-1">Format attendu: JJ/MM/AAAA HH:MM (ex: 02/07/2026 09:54)</p>
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
                                    @if(auth()->user()->hasRole('Admin'))
                                    <th class="px-6 py-3 text-right">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium">{{ \Carbon\Carbon::parse($log->horodatage)->format('d/m/Y H:i:s') }}</td>
                                        <td class="px-6 py-4">{{ $log->user?->nom_complet ?? 'Inconnu' }}</td>
                                        <td class="px-6 py-4">{{ $log->zone?->nom_salle ?? 'Zone inconnue' }}</td>
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
                                        @if(auth()->user()->hasRole('Admin'))
                                        <td class="px-6 py-4 text-right space-x-2">
                                            @can('historique-passage-edit')
                                                <a href="{{ route('historique_passages.edit', $log->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Modifier</a>
                                            @endcan
                                            @can('historique-passage-delete')
                                                <form action="{{ route('historique_passages.destroy', $log->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cet historique ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">Supprimer</button>
                                                </form>
                                            @endcan
                                        </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole('Admin') ? '5' : '4' }}" class="px-6 py-8 text-center text-gray-500">Aucun historique d'accès disponible.</td>
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