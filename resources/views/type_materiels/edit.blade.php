<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Modifier le Type de Matériel #{{ $type->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('type_materiels.update', $type->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="libelle_type" value="Titre du type *" />
                        <x-text-input id="libelle_type" name="libelle_type" type="text" class="mt-1 block w-full" :value="old('libelle_type', $type->libelle_type)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('libelle_type')" />
                    </div>

                    <div>
                        <x-input-label for="description_type" value="Description" />
                        <textarea id="description_type" name="description_type" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description_type', $type->description_type) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description_type')" />
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('type_materiels.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 transition">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 transition">Enregistrer</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
