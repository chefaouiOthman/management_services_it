@php
    $isEdit = isset($projet) && $projet->exists;
    $actionUrl = $isEdit ? route('projets.update', $projet->id) : route('projets.store');
@endphp

<form method="POST" action="{{ $actionUrl }}" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informations du Projet</h3>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nom Projet -->
            <div>
                <x-input-label for="nom_projet" value="Nom du Projet *" />
                <x-text-input id="nom_projet" name="nom_projet" type="text" class="mt-1 block w-full" :value="old('nom_projet', $projet->nom_projet ?? '')" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('nom_projet')" />
            </div>

            <!-- Client -->
            <div>
                <x-input-label for="client_id" value="Client Commanditaire *" />
                <select id="client_id" name="client_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    <option value="">-- Sélectionner un client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->user_id }}" @selected(old('client_id', $projet->client_id ?? '') == $client->user_id)>
                            {{ $client->user?->nom_complet ?? 'Client sans profil (' . ($client->nom_societe ?? 'Physique') . ')' }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('client_id')" />
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <x-input-label for="description" value="Description Globale *" />
                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('description', $projet->description ?? '') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <!-- Budget -->
            <div>
                <x-input-label for="budget_vendu" value="Budget Vendu (MAD) *" />
                <x-text-input id="budget_vendu" name="budget_vendu" type="number" step="0.01" class="mt-1 block w-full font-mono" :value="old('budget_vendu', $projet->budget_vendu ?? '')" required />
                <x-input-error class="mt-2" :messages="$errors->get('budget_vendu')" />
            </div>

            <!-- Statut Projet -->
            <div>
                <x-input-label for="statut_projet" value="Statut Actuel *" />
                <select id="statut_projet" name="statut_projet" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    @foreach(['analyse', 'developpement', 'recette', 'deploie', 'maintenance'] as $statut)
                        <option value="{{ $statut }}" @selected(old('statut_projet', $projet->statut_projet ?? '') == $statut)>
                            {{ ucfirst($statut) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('statut_projet')" />
            </div>
            
            <!-- Technologies -->
            <div class="md:col-span-2">
                <x-input-label value="Stack Technologique" />
                <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    @php
                        $selectedTechs = old('technologies', $isEdit ? $projet->technologies?->pluck('id')->toArray() ?? [] : []);
                    @endphp
                    @foreach($technologies as $tech)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="technologies[]" value="{{ $tech->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700" 
                                @checked(in_array($tech->id, $selectedTechs))>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $tech->nom_tech }} <span class="text-xs text-gray-500">({{ $tech->version }})</span></span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-3">
                    <x-input-label for="new_technologies" value="+ Ajouter des nouvelles technologies (séparées par une virgule)" />
                    <x-text-input id="new_technologies" name="new_technologies" type="text" class="mt-1 block w-full text-sm" placeholder="Ex: React 18, Node.js, Docker" :value="old('new_technologies')" />
                    <p class="text-xs text-gray-500 mt-1">Elles seront créées automatiquement et associées à ce projet.</p>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('technologies')" />
                <x-input-error class="mt-2" :messages="$errors->get('new_technologies')" />
            </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('projets.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Annuler
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }}
            </button>
        </div>
    </x-card>
</form>
