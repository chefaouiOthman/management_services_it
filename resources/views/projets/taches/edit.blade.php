<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Modifier la Tâche : ') }} {{ $tachePivot->titre_tache }}
            <br>
            <span class="text-sm text-gray-500 font-normal">Dans le projet : {{ $projet->nom_projet }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('projets.show', $projet->id) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour au projet
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('projets.taches.update', [$projet->id, $tachePivot->id]) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg mb-6 border border-gray-200 dark:border-gray-600">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <strong>Information :</strong> Vous modifiez ici uniquement les attributs de cette tâche relatifs au projet <strong>{{ $projet->nom_projet }}</strong>.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Priorité pour ce projet -->
                            <div>
                                <x-input-label for="priorite" value="Priorité" />
                                <select id="priorite" name="priorite" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                    <option value="basse" {{ old('priorite', $tachePivot->pivot?->priorite ?? 'N/A') == 'basse' ? 'selected' : '' }}>Basse</option>
                                    <option value="moyenne" {{ old('priorite', $tachePivot->pivot?->priorite ?? 'N/A') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                    <option value="haute" {{ old('priorite', $tachePivot->pivot?->priorite ?? 'N/A') == 'haute' ? 'selected' : '' }}>Haute</option>
                                    <option value="bloquante" {{ old('priorite', $tachePivot->pivot?->priorite ?? 'N/A') == 'bloquante' ? 'selected' : '' }}>Bloquante</option>
                                </select>
                                <x-input-error :messages="$errors->get('priorite')" class="mt-2" />
                            </div>

                            <!-- Statut pour ce projet -->
                            <div>
                                <x-input-label for="statut_tache" value="Statut" />
                                <select id="statut_tache" name="statut_tache" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                    <option value="backlog" {{ old('statut_tache', $tachePivot->pivot?->statut_tache ?? 'N/A') == 'backlog' ? 'selected' : '' }}>Backlog (À faire)</option>
                                    <option value="en_cours" {{ old('statut_tache', $tachePivot->pivot?->statut_tache ?? 'N/A') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="en_revue" {{ old('statut_tache', $tachePivot->pivot?->statut_tache ?? 'N/A') == 'en_revue' ? 'selected' : '' }}>En revue</option>
                                    <option value="termine" {{ old('statut_tache', $tachePivot->pivot?->statut_tache ?? 'N/A') == 'termine' ? 'selected' : '' }}>Terminé</option>
                                </select>
                                <x-input-error :messages="$errors->get('statut_tache')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <!-- Suppression Option -->
                            <button type="button" class="text-red-600 hover:text-red-900 text-sm font-medium" onclick="if(confirm('Êtes-vous sûr de vouloir retirer cette tâche du projet ?')) document.getElementById('delete-form').submit();">
                                Retirer la tâche du projet
                            </button>

                            <x-primary-button class="ml-4">
                                {{ __('Mettre à jour') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Formulaire de suppression caché -->
                    <form id="delete-form" action="{{ route('projets.taches.destroy', [$projet->id, $tachePivot->id]) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
