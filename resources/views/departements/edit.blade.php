<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Modifier le Département
            </h2>
            <a href="{{ route('departements.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                ← Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Modifier : {{ $departement->nom_departement }}</h3>

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('departements.update', $departement->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nom_departement" value="Nom du Département *" />
                        <x-text-input
                            id="nom_departement"
                            name="nom_departement"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('nom_departement', $departement->nom_departement)"
                            required
                        />
                        <x-input-error :messages="$errors->get('nom_departement')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('departements.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md font-semibold text-sm hover:bg-gray-200 transition">
                            Annuler
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 transition">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
