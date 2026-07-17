<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Historique des Passages
            </h2>
            @can('historique-passage-create')
            <a href="{{ route('historique_passages.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Nouvelle entrée
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('success') }}</div>
            @endif

            <x-search-filters :search="request('search')" searchPlaceholder="Rechercher par utilisateur, zone..."
                :filters="[
                    'date_debut' => ['label' => 'Date début', 'type' => 'date'],
                    'date_fin' => ['label' => 'Date fin', 'type' => 'date'],
                ]" />

            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Date/Heure</th>
                                <th class="px-6 py-3">Utilisateur</th>
                                <th class="px-6 py-3">Zone</th>
                                <th class="px-6 py-3">Statut</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historiques as $log)
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
                                            <x-badge type="gray">{{ $log->tentative_statut }}</x-badge>
                                        @endif
                                    </td>
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
                    {{ $historiques->appends(request()->query())->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
