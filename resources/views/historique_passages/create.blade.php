<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Nouvelle Entrée d'Accès
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('historique_passages.store') }}" method="POST" class="space-y-6" x-data="{ useCurrentTime: true }">
                    @csrf

                    <div>
                        <x-input-label for="user_id" value="Utilisateur" />
                        <select name="user_id" id="user_id" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Sélectionner un utilisateur --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>{{ $u->nom_complet }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="zone_id" value="Zone" />
                        <select name="zone_id" id="zone_id" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Sélectionner une zone --</option>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}" @selected(old('zone_id') == $z->id)>{{ $z->nom_salle }} ({{ $z->code_zone }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('zone_id')" class="mt-1" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <x-input-label for="horodatage" value="Horodatage" />
                            <label class="text-xs flex items-center text-gray-500 dark:text-gray-400">
                                <input type="checkbox" x-model="useCurrentTime" class="mr-1 rounded"> Heure actuelle
                            </label>
                        </div>
                        <input type="text" name="horodatage" id="horodatage"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            placeholder="JJ/MM/AAAA HH:MM"
                            x-bind:disabled="useCurrentTime"
                            :value="useCurrentTime ? '' : '{{ now()->format('d/m/Y H:i') }}'">
                        <p class="text-xs text-gray-500 mt-1">Format attendu: JJ/MM/AAAA HH:MM (ex: 02/07/2026 09:54)</p>
                        <x-input-error :messages="$errors->get('horodatage')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="tentative_statut" value="Statut" />
                        <select name="tentative_statut" id="tentative_statut" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="autorise" @selected(old('tentative_statut') == 'autorise')>Autorisé</option>
                            <option value="refuse_niveau_insuffisant" @selected(old('tentative_statut') == 'refuse_niveau_insuffisant')>Refusé (Niveau insuffisant)</option>
                            <option value="refuse_zone_desactivee" @selected(old('tentative_statut') == 'refuse_zone_desactivee')>Refusé (Zone désactivée)</option>
                        </select>
                        <x-input-error :messages="$errors->get('tentative_statut')" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('zones.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium hover:bg-gray-200">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold hover:bg-indigo-700">Enregistrer</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
