<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Hub Projets
            </h2>
            @can('projet-create')
            <a href="{{ route('projets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Nouveau Projet
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
<x-search-filters :search="request('search')" searchPlaceholder="Rechercher par nom, client, statut..."
    :filters="[
        'statut' => ['label' => 'Statut', 'options' => ['planifié' => 'Planifié', 'en_cours' => 'En cours', 'en_pause' => 'En pause', 'terminé' => 'Terminé', 'annulé' => 'Annulé']],
    ]" />

            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Projet</th>
                                <th scope="col" class="px-6 py-3">Client</th>
                                <th scope="col" class="px-6 py-3">Statut</th>
                                <th scope="col" class="px-6 py-3">Budget</th>
                                <th scope="col" class="px-6 py-3">Tech Stack</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projets as $projet)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <a href="{{ route('projets.show', $projet->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-base font-semibold">
                                                {{ $projet->nom_projet }}
                                            </a>
                                            <span class="text-xs text-gray-500">{{ $projet->taches_count }} Tâches</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $projet->client?->user?->nom_complet ?? 'N/A' }}
                                        <br>
                                        <span class="text-xs text-gray-400">{{ $projet->client?->nom_societe ?? 'Client Physique' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statutColor = match($projet->statut_projet) {
                                                'analyse'       => 'info',
                                                'developpement' => 'warning',
                                                'recette'       => 'primary',
                                                'deploie'       => 'success',
                                                'maintenance'   => 'gray',
                                                default         => 'gray',
                                            };
                                        @endphp
                                        <x-badge :type="$statutColor">
                                            {{ ucfirst($projet->statut_projet) }}
                                        </x-badge>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-gray-900 dark:text-gray-300">
                                        {{ number_format($projet->budget_vendu, 2, ',', ' ') }} DHS
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($projet->technologies as $tech)
                                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                                    {{ $tech->nom_tech }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('projets.show', $projet->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm">
                                            Voir détails
                                        </a>
                                        @can('projet-edit')
                                        <a href="{{ route('projets.edit', $projet->id) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 font-medium text-sm">
                                            Éditer
                                        </a>
                                        @endcan
                                        @can('projet-delete')
                                        <form action="{{ route('projets.destroy', $projet->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Supprimer</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Aucun projet trouvé.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $projets->appends(request()->query())->links() }}
            </x-card>
        </div>
    </div>
</x-app-layout>
