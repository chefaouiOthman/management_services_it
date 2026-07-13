<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Modifier la Catégorie : {{ $categorie->libelle_categorie }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('categorie_flux.update', $categorie->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="libelle_categorie" value="Libellé Catégorie *" />
                        <x-text-input id="libelle_categorie" name="libelle_categorie" required class="mt-1 block w-full" :value="old('libelle_categorie', $categorie->libelle_categorie)" />
                        <x-input-error class="mt-2" :messages="$errors->get('libelle_categorie')" />
                    </div>
                    <div>
                        <x-input-label for="code_comptable" value="Code Comptable" />
                        <x-text-input id="code_comptable" name="code_comptable" class="mt-1 block w-full" :value="old('code_comptable', $categorie->code_comptable)" placeholder="Ex: 701" />
                        <x-input-error class="mt-2" :messages="$errors->get('code_comptable')" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('categorie_flux.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold hover:bg-indigo-700 transition">Mettre à jour</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
