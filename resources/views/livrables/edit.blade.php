<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Modifier le Livrable : ') }} {{ $livrable->titre_jalon }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('livrables.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour aux livrables
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('livrables.update', $livrable->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-full">
                                <x-input-label for="projet_id" value="Projet Associé" />
                                <select id="projet_id" name="projet_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- Sélectionner un projet --</option>
                                    @foreach($projets as $projet)
                                        <option value="{{ $projet->id }}" {{ old('projet_id', $livrable->projet_id) == $projet->id ? 'selected' : '' }}>
                                            {{ $projet->nom_projet }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('projet_id')" class="mt-2" />
                            </div>

                            <div class="col-span-full">
                                <x-input-label for="titre_jalon" value="Titre du Jalon / Livrable" />
                                <x-text-input id="titre_jalon" name="titre_jalon" type="text" class="mt-1 block w-full" value="{{ old('titre_jalon', $livrable->titre_jalon) }}" required />
                                <x-input-error :messages="$errors->get('titre_jalon')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="date_limite_soumission" value="Date Limite de Soumission" />
                                <x-text-input id="date_limite_soumission" name="date_limite_soumission" type="date" class="mt-1 block w-full" value="{{ old('date_limite_soumission', $livrable->date_limite_soumission ? $livrable->date_limite_soumission->format('Y-m-d') : '') }}" required />
                                <x-input-error :messages="$errors->get('date_limite_soumission')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="statut_client" value="Statut Client" />
                                <select id="statut_client" name="statut_client" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                    <option value="en_attente" {{ old('statut_client', $livrable->statut_client) == 'en_attente' ? 'selected' : '' }}>En attente de validation</option>
                                    <option value="rejete_avec_corrections" {{ old('statut_client', $livrable->statut_client) == 'rejete_avec_corrections' ? 'selected' : '' }}>Rejeté avec corrections</option>
                                    <option value="valide" {{ old('statut_client', $livrable->statut_client) == 'valide' ? 'selected' : '' }}>Validé</option>
                                </select>
                                <x-input-error :messages="$errors->get('statut_client')" class="mt-2" />
                            </div>

                            <div class="col-span-full">
                                <x-input-label for="fichier" value="Fichier joint (Optionnel)" />
                                @if($livrable->fichier_path)
                                    <div class="mt-2 mb-4 p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">
                                                Fichier actuel : {{ $livrable->fichier_nom_original ?? 'Document existant' }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2">Sélectionnez un nouveau fichier uniquement si vous souhaitez remplacer l'existant.</p>
                                @endif
                                <input type="file" id="fichier" name="fichier" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300" />
                                <x-input-error :messages="$errors->get('fichier')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <x-primary-button class="ml-4">
                                {{ __('Mettre à jour') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
