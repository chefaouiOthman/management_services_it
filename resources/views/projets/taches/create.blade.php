<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ajouter une Tâche au Projet : ') }} {{ $projet->nom_projet }}
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
                    <form action="{{ route('projets.taches.store', $projet->id) }}" method="POST" class="space-y-6">
                        @csrf

                        <div x-data="{ mode: 'existant' }" class="space-y-6">
                            <!-- Choix du mode : Existant vs Nouveau -->
                            <div class="flex items-center space-x-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" x-model="mode" value="existant" class="form-radio text-indigo-600">
                                    <span class="ml-2">Associer une tâche existante</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" x-model="mode" value="nouveau" class="form-radio text-indigo-600">
                                    <span class="ml-2">Créer une nouvelle tâche</span>
                                </label>
                            </div>

                            <!-- Sélection d'une tâche existante -->
                            <div x-show="mode === 'existant'" x-transition>
                                <x-input-label for="tache_id" value="Sélectionner une tâche" />
                                <select id="tache_id" name="tache_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" x-bind:required="mode === 'existant'">
                                    <option value="">-- Choisir une tâche --</option>
                                    @foreach($tachesExistantes as $t)
                                        <option value="{{ $t->id }}" {{ old('tache_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->titre_tache }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('tache_id')" class="mt-2" />
                            </div>

                            <!-- Création d'une nouvelle tâche -->
                            <div x-show="mode === 'nouveau'" x-transition style="display: none;">
                                <x-input-label for="titre_tache_new" value="Titre de la nouvelle tâche" />
                                <x-text-input id="titre_tache_new" name="titre_tache_new" type="text" class="mt-1 block w-full" value="{{ old('titre_tache_new') }}" x-bind:required="mode === 'nouveau'" placeholder="Ex: Développer l'API de paiement" />
                                <x-input-error :messages="$errors->get('titre_tache_new')" class="mt-2" />
                            </div>

                            <hr class="border-gray-200 dark:border-gray-700 my-4" />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Priorité pour ce projet -->
                                <div>
                                    <x-input-label for="priorite" value="Priorité dans ce projet" />
                                    <select id="priorite" name="priorite" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                        <option value="basse" {{ old('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                                        <option value="moyenne" {{ old('priorite', 'moyenne') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                        <option value="haute" {{ old('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                                        <option value="bloquante" {{ old('priorite') == 'bloquante' ? 'selected' : '' }}>Bloquante</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('priorite')" class="mt-2" />
                                </div>

                                <!-- Statut initial pour ce projet -->
                                <div>
                                    <x-input-label for="statut_tache" value="Statut de la tâche" />
                                    <select id="statut_tache" name="statut_tache" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                        <option value="backlog" {{ old('statut_tache', 'backlog') == 'backlog' ? 'selected' : '' }}>Backlog (À faire)</option>
                                        <option value="en_cours" {{ old('statut_tache') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                        <option value="en_revue" {{ old('statut_tache') == 'en_revue' ? 'selected' : '' }}>En revue</option>
                                        <option value="termine" {{ old('statut_tache') == 'termine' ? 'selected' : '' }}>Terminé</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('statut_tache')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <x-primary-button class="ml-4">
                                {{ __('Enregistrer l\'association') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
