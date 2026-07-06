<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nouvelle Technologie
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('technologies.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="nom_tech" value="Nom de la Technologie *" />
                        <x-text-input name="nom_tech" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="version" value="Version *" />
                        <x-text-input name="version" required class="mt-1 block w-full" />
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('technologies.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Enregistrer</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
