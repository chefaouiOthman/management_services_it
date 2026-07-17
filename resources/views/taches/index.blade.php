<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Toutes les Tâches
            </h2>
            <a href="{{ route('taches.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold">
                + Nouvelle Tâche
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

<x-search-filters :search="request('search')" searchPlaceholder="Rechercher par titre, statut, priorité..."
    :filters="[
        'statut' => ['label' => 'Statut', 'options' => ['à_faire' => 'À faire', 'en_cours' => 'En cours', 'terminée' => 'Terminée', 'en_pause' => 'En pause']],
        'priorite' => ['label' => 'Priorité', 'options' => ['basse' => 'Basse', 'moyenne' => 'Moyenne', 'haute' => 'Haute', 'critique' => 'Critique']],
    ]" />

            <x-card>
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Titre</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taches as $tache)
                            <tr class="border-b last:border-0 hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $tache->id }}</td>
                                <td class="px-6 py-4 font-bold">{{ $tache->titre_tache }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @can('tache-view')
                                        <a href="{{ route('taches.show', $tache->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs">Voir les détails</a>
                                    @endcan
                                    @can('tache-edit')
                                        <a href="{{ route('taches.edit', $tache->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Modifier</a>
                                    @endcan
                                    @can('tache-delete')
                                        <form action="{{ route('taches.destroy', $tache->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette tâche ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">Supprimer</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucune tâche.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $taches->appends(request()->query())->links() }}
            </x-card>
        </div>
    </div>
</x-app-layout>
