<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Saisir des heures - Projet : ') }} {{ $projet->nom_projet }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('projets.show', $projet->id) }}#timesheets" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour au projet
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('projets.feuille_temps.store', $projet->id) }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Sélection de l'employé (Seul l'Admin peut choisir un autre employé) -->
                            <div>
                                <x-input-label for="employe_id" value="Employé" />
                                <select id="employe_id" name="employe_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required {{ Auth::user()->hasRole('Admin') ? '' : 'readonly' }}>
                                    @foreach($employes as $employe)
                                        <option value="{{ $employe->user_id }}" {{ (old('employe_id', Auth::id()) == $employe->user_id) ? 'selected' : '' }}>
                                            {{ $employe->user?->nom_complet ?? 'Employé introuvable' }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('employe_id')" class="mt-2" />
                            </div>

                            <!-- Date de l'effort -->
                            <div>
                                <x-input-label for="date_effort" value="Date de la saisie" />
                                <x-text-input id="date_effort" name="date_effort" type="date" class="mt-1 block w-full" value="{{ old('date_effort', date('Y-m-d')) }}" required />
                                <x-input-error :messages="$errors->get('date_effort')" class="mt-2" />
                            </div>

                            <!-- Durée totale en heures -->
                            <div>
                                <x-input-label for="duree_heures" value="Durée totale (en heures)" />
                                <x-text-input id="duree_heures" name="duree_heures" type="number" step="0.5" min="0.5" max="24" class="mt-1 block w-full" value="{{ old('duree_heures') }}" placeholder="Ex: 5.5" required />
                                <x-input-error :messages="$errors->get('duree_heures')" class="mt-2" />
                            </div>

                            <!-- Tâches associées (Choix multiple) -->
                            <div class="col-span-1 md:col-span-2 border border-gray-200 dark:border-gray-700 p-4 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                <x-input-label value="Tâches associées à cette journée (Optionnel)" class="mb-3 text-lg font-medium" />
                                
                                @if($taches->isEmpty())
                                    <p class="text-sm text-gray-500 italic">Aucune tâche n'est actuellement associée à ce projet.</p>
                                @else
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-60 overflow-y-auto p-2">
                                        @foreach($taches as $tache)
                                            <label class="flex items-start space-x-3 cursor-pointer p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                                <input type="checkbox" name="taches[]" value="{{ $tache->id }}" class="mt-1 form-checkbox text-indigo-600 border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                    {{ is_array(old('taches')) && in_array($tache->id, old('taches')) ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $tache->titre_tache }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                <x-input-error :messages="$errors->get('taches')" class="mt-2" />
                            </div>

                            <!-- Commentaire -->
                            <div class="col-span-full">
                                <x-input-label for="commentaire" value="Commentaire / Description de l'activité *" />
                                <textarea id="commentaire" name="commentaire" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>{{ old('commentaire') }}</textarea>
                                <x-input-error :messages="$errors->get('commentaire')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <x-primary-button class="ml-4">
                                {{ __('Enregistrer la feuille de temps') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
