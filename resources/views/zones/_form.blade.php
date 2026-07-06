@props(['zone' => null])

@php $isEdit = isset($zone) && $zone !== null; @endphp

<form method="POST" action="{{ $isEdit ? route('zones.update', $zone->id) : route('zones.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="space-y-6">
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Caractéristiques de la Zone</h3>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="code_zone" value="Code Zone (Unique)" />
                    <x-text-input id="code_zone" name="code_zone" type="text" class="mt-1 block w-full" :value="old('code_zone', $zone->code_zone ?? '')" required maxlength="50" />
                    <x-input-error :messages="$errors->get('code_zone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nom_salle" value="Nom de la Salle" />
                    <x-text-input id="nom_salle" name="nom_salle" type="text" class="mt-1 block w-full" :value="old('nom_salle', $zone->nom_salle ?? '')" required maxlength="100" />
                    <x-input-error :messages="$errors->get('nom_salle')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="niveau_requis" value="Niveau d'Habilitation Requis" />
                    <x-text-input id="niveau_requis" name="niveau_requis" type="number" min="0" class="mt-1 block w-full" :value="old('niveau_requis', $zone->niveau_requis ?? 1)" required />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">0 = Accès libre, plus le chiffre est élevé, plus l'accès est restreint.</p>
                    <x-input-error :messages="$errors->get('niveau_requis')" class="mt-2" />
                </div>

                <div class="flex items-center mt-6">
                    <input id="est_active" name="est_active" type="checkbox" value="1"
                        {{ old('est_active', $zone->est_active ?? true) ? 'checked' : '' }}
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                    <label for="est_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">Zone Active (accessible aux passages)</label>
                    <x-input-error :messages="$errors->get('est_active')" class="mt-2" />
                </div>
            </div>
        </x-card>

        <div class="flex justify-between pt-2">
            <a href="{{ route('zones.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                Annuler
            </a>
            <x-primary-button>{{ $isEdit ? 'Mettre à jour la Zone' : 'Créer la Zone' }}</x-primary-button>
        </div>
    </div>
</form>
