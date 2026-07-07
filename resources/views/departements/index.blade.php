<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Structure Organisationnelle (Départements)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Ajouter un Département -->
                <x-card class="md:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">➕ Ajouter un Département</h3>
                    <form action="{{ route('departements.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="nom_departement" value="Nom du Département *" />
                            <x-text-input name="nom_departement" required class="mt-1 block w-full" placeholder="Ex: Ressources Humaines, IT..." />
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 transition">Enregistrer</button>
                    </form>
                </x-card>

                <!-- Liste des Départements -->
                <x-card class="md:col-span-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">🏢 Départements Existants</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">ID</th>
                                    <th scope="col" class="px-6 py-3">Nom du Département</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departements ?? \App\Models\Departement::all() as $dept)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 font-mono font-bold">{{ $dept->id }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $dept->nom_departement }}</td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <a href="{{ route('departements.show', $dept->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Voir les membres</a>
                                            <a href="{{ route('departements.edit', $dept->id) }}" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">Modifier</a>
                                            <form action="{{ route('departements.destroy', $dept->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Confirmer la suppression de {{ $dept->nom_departement }} ?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucun département configuré.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

        </div>
    </div>
</x-app-layout>
