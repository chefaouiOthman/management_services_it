<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Supports de Cours
            </h2>
            @can('support-cours-create')
            <a href="{{ route('supports.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Nouveau Support
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

<x-search-filters :search="request('search')" searchPlaceholder="Rechercher par titre, type de fichier..."
    :filters="[
        'type' => ['label' => 'Type', 'options' => ['pdf' => 'PDF', 'video' => 'Vidéo', 'document' => 'Document', 'presentation' => 'Présentation', 'image' => 'Image']],
    ]" />

            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Nom du fichier</th>
                                <th class="px-6 py-3">Formations associées</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supports as $support)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $support->nom_fichier }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($support->catalogueFormations?->isNotEmpty() ?? false)
                                            @foreach($support->catalogueFormations as $catalogue)
                                                <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded mb-1 border border-gray-200 dark:border-gray-600 text-xs">
                                                    {{ $catalogue->titre_formation }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-gray-400 italic">Aucune</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('supports.show', $support->id) }}" class="text-blue-600 dark:text-blue-500 hover:underline font-medium text-xs">Voir</a>
                                        <a href="{{ route('supports.download', $support->id) }}" class="text-green-600 dark:text-green-500 hover:underline font-medium text-xs">Télécharger</a>
                                        @can('support-cours-edit')
                                            <a href="{{ route('supports.edit', $support->id) }}" class="text-indigo-600 dark:text-indigo-500 hover:underline font-medium text-xs">Modifier</a>
                                        @endcan
                                        @can('support-cours-delete')
                                            <form action="{{ route('supports.destroy', $support->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer ce support ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-500 hover:underline font-medium text-xs">Supprimer</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">Aucun support de cours trouvé.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $supports->appends(request()->query())->links() }}
            </x-card>
        </div>
    </div>
</x-app-layout>
