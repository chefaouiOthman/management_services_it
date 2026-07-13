<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifier la Technologie : {{ $technologie->nom_tech }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('technologies.update', $technologie->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="nom_tech" value="Nom de la Technologie *" />
                        <x-text-input id="nom_tech" name="nom_tech" value="{{ old('nom_tech', $technologie->nom_tech) }}" required class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('nom_tech')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="version" value="Version *" />
                        <x-text-input id="version" name="version" value="{{ old('version', $technologie->version) }}" required class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('version')" class="mt-2" />
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('technologies.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Mettre à jour</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
