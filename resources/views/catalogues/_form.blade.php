@php
    $isEdit = isset($catalogue);
    $actionUrl = $isEdit ? route('catalogue.update', $catalogue->id) : route('catalogue.store');
@endphp

<form method="POST" action="{{ $actionUrl }}" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Détails du Programme</h3>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Titre Formation -->
            <div class="md:col-span-2">
                <x-input-label for="titre_formation" value="Titre de la Formation *" />
                <x-text-input id="titre_formation" name="titre_formation" type="text" class="mt-1 block w-full" :value="old('titre_formation', $catalogue->titre_formation ?? '')" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('titre_formation')" />
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <x-input-label for="description_programme" value="Description du Programme *" />
                <textarea id="description_programme" name="description_programme" rows="5" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('description_programme', $catalogue->description_programme ?? '') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description_programme')" />
            </div>

            <!-- Prix -->
            <div>
                <x-input-label for="prix_standard" value="Prix Standard (MAD) *" />
                <x-text-input id="prix_standard" name="prix_standard" type="number" step="0.01" class="mt-1 block w-full font-mono" :value="old('prix_standard', $catalogue->prix_standard ?? '')" required />
                <x-input-error class="mt-2" :messages="$errors->get('prix_standard')" />
            </div>

            <!-- Supports de cours (Pivot N,N) -->
            <div class="md:col-span-2">
                <x-input-label value="Supports de Cours Associés" />
                <p class="text-sm text-gray-500 mb-2">Sélectionnez les documents pédagogiques liés à ce programme.</p>
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto">
                    @php
                        $selectedSupports = old('supports', isset($catalogue) ? $catalogue->supportCours->pluck('id')->toArray() : []);
                    @endphp
                    @forelse($supports as $support)
                        <label class="inline-flex items-center p-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer transition">
                            <input type="checkbox" name="supports[]" value="{{ $support->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700" 
                                @checked(in_array($support->id, $selectedSupports))>
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-200 truncate">{{ $support->nom_fichier }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500 italic">Aucun support de cours disponible dans la base.</p>
                    @endforelse
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('supports')" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('catalogue.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                Annuler
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                {{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }}
            </button>
        </div>
    </x-card>
</form>
