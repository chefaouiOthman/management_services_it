<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Toutes les Feuilles de Temps') }}
            </h2>
            @can('feuille-temps-create')
            <div class="flex items-center gap-3">
                <form action="{{ route('feuille_temps.select_project') }}" method="GET" class="flex items-center gap-2">
                    @csrf
                    <select name="projet_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Sélectionner un projet...</option>
                        @foreach($projets as $projet)
                            <option value="{{ $projet->id }}">{{ $projet->nom_projet }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold hover:bg-indigo-700 transition">
                        Créer une feuille de temps
                    </button>
                </form>
            </div>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages de succès/erreur -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">ID</th>
                                    <th scope="col" class="px-6 py-3">Employé</th>
                                    <th scope="col" class="px-6 py-3">Projet</th>
                                    <th scope="col" class="px-6 py-3">Date d'Effort</th>
                                    <th scope="col" class="px-6 py-3">Durée</th>
                                    <th scope="col" class="px-6 py-3">Tâches</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feuilles as $feuille)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            #{{ $feuille->id }}
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ $feuille->employe?->user?->nom_complet ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-900 dark:text-white">
                                            {{ $feuille->projet?->nom_projet ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($feuille->date_effort)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 font-bold text-gray-800 dark:text-gray-200">
                                            {{ $feuille->duree_heures }} h
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            @forelse($feuille->taches as $tache)
                                                <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded mb-1 border border-gray-200 dark:border-gray-600">
                                                    {{ $tache->titre_tache }}
                                                </span>
                                            @empty
                                                <span class="italic text-gray-400">Aucune</span>
                                            @endforelse
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                            @can('feuille-temps-view')
                                                <a href="{{ route('feuille_temps.show', $feuille->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Voir</a>
                                            @endcan
                                            @if(auth()->user()->hasRole('Admin'))
                                                <a href="{{ route('feuille_temps.edit', $feuille->id) }}" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">Modifier</a>
                                            @endif
                                            @can('feuille-temps-delete')
                                                <form action="{{ route('feuille_temps.destroy', $feuille->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Confirmez-vous la suppression de cette feuille de temps ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Aucune feuille de temps n'a été trouvée.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
