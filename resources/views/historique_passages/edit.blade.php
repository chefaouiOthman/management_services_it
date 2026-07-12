<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Modifier l'Historique de Passage
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('zones.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour aux zones
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('historique_passages.update', $historique->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="user_id" value="Utilisateur" />
                                <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $historique->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->nom_complet }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="zone_id" value="Zone" />
                                <select id="zone_id" name="zone_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" {{ old('zone_id', $historique->zone_id) == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->nom_salle }} ({{ $zone->code_zone }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('zone_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="horodatage" value="Horodatage" />
                                <x-text-input id="horodatage" name="horodatage" type="text" class="mt-1 block w-full" value="{{ old('horodatage', \Carbon\Carbon::parse($historique->horodatage)->format('d/m/Y H:i')) }}" placeholder="Format: JJ/MM/AAAA HH:MM" required />
                                <x-input-error :messages="$errors->get('horodatage')" class="mt-2" />
                                <p class="text-xs text-gray-500 mt-1">Format attendu: JJ/MM/AAAA HH:MM (ex: 02/07/2026 09:54)</p>
                            </div>

                            <div>
                                <x-input-label for="tentative_statut" value="Statut" />
                                <select id="tentative_statut" name="tentative_statut" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                    <option value="autorise" {{ old('tentative_statut', $historique->tentative_statut) == 'autorise' ? 'selected' : '' }}>Autorisé</option>
                                    <option value="refuse_niveau_insuffisant" {{ old('tentative_statut', $historique->tentative_statut) == 'refuse_niveau_insuffisant' ? 'selected' : '' }}>Refusé (Niveau insuffisant)</option>
                                    <option value="refuse_zone_desactivee" {{ old('tentative_statut', $historique->tentative_statut) == 'refuse_zone_desactivee' ? 'selected' : '' }}>Refusé (Zone désactivée)</option>
                                </select>
                                <x-input-error :messages="$errors->get('tentative_statut')" class="mt-2" />
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
