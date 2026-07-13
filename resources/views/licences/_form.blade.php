<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ (isset($licence) && $licence->exists) ? 'Éditer Licence : ' . $licence->nom_logiciel : 'Nouvelle Licence' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ (isset($licence) && $licence->exists) ? route('licences.update', $licence->id) : route('licences.store') }}" method="POST" class="space-y-6">
                    @csrf
                    @if(isset($licence) && $licence->exists)
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="nom_logiciel" value="Nom du Logiciel *" />
                            <x-text-input id="nom_logiciel" name="nom_logiciel" type="text" class="mt-1 block w-full" :value="old('nom_logiciel', $licence->nom_logiciel ?? '')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('nom_logiciel')" />
                        </div>

                        <div>
                            <x-input-label for="cle_licence" value="Clé de Licence *" />
                            <x-text-input id="cle_licence" name="cle_licence" type="text" class="mt-1 block w-full" :value="old('cle_licence', $licence->cle_licence ?? '')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('cle_licence')" />
                        </div>

                        <div>
                            <x-input-label for="date_expiration" value="Date d'Expiration *" />
                            <x-text-input id="date_expiration" name="date_expiration" type="date" class="mt-1 block w-full" :value="old('date_expiration', (isset($licence) && $licence->exists) ? $licence->date_expiration?->format('Y-m-d') ?? '' : '')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('date_expiration')" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('licences.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 transition">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 transition">Enregistrer</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
