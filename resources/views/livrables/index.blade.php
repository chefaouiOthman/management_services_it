<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Livrables & Jalons') }}
            </h2>
            @can('livrable-create')
            <a href="{{ route('livrables.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Nouveau Livrable
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<x-search-filters :search="request('search')" searchPlaceholder="Rechercher par titre, projet, statut..."
    :filters="[
        'statut' => ['label' => 'Statut', 'options' => ['en_attente' => 'En attente', 'soumis' => 'Soumis', 'validé' => 'Validé', 'rejeté' => 'Rejeté']],
    ]" />

            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Titre</th>
                                <th scope="col" class="px-6 py-3">Projet</th>
                                <th scope="col" class="px-6 py-3">Date Limite</th>
                                <th scope="col" class="px-6 py-3">Statut</th>
                                <th scope="col" class="px-6 py-3">Fichier</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($livrables as $livrable)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $livrable->titre_jalon }}</td>
                                    <td class="px-6 py-4">{{ $livrable->projet?->nom_projet ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $livrable->date_limite_soumission?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $color = match($livrable->statut_client) {
                                                'valide' => 'success',
                                                'rejete_avec_corrections' => 'danger',
                                                default => 'warning'
                                            };
                                        @endphp
                                        <x-badge :type="$color">{{ str_replace('_', ' ', $livrable->statut_client) }}</x-badge>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($livrable->fichier_path)
                                            <a href="{{ route('livrables.download', $livrable->id) }}" class="text-indigo-600 hover:underline text-xs flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                {{ $livrable->fichier_nom_original ?? 'Télécharger' }}
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic">Aucun</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @can('livrable-view')
                                        <a href="{{ route('livrables.show', $livrable->id) }}" class="font-medium text-blue-600 hover:underline mr-3">Voir</a>
                                        @endcan
                                        @can('livrable-edit')
                                        <a href="{{ route('livrables.edit', $livrable->id) }}" class="font-medium text-indigo-600 hover:underline mr-3">Modifier</a>
                                        @endcan
                                        @can('livrable-delete')
                                        <form action="{{ route('livrables.destroy', $livrable->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer ce livrable ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="font-medium text-red-600 hover:underline">Supprimer</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun livrable trouvé.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $livrables->appends(request()->query())->links() }}
            </x-card>
        </div>
    </div>
</x-app-layout>
