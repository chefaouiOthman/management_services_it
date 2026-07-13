<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $technologie->nom_tech }} {{ $technologie->version }}
                </h2>
            </div>
            <div class="flex gap-2">
                @can('technologie-edit')
                <a href="{{ route('technologies.edit', $technologie->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    Modifier
                </a>
                @endcan
                <a href="{{ route('technologies.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    &larr; Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Nom</p>
                        <p class="font-medium text-gray-900">{{ $technologie->nom_tech }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Version</p>
                        <p class="font-medium text-gray-900 font-mono">{{ $technologie->version }}</p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header"><h3 class="text-lg font-medium text-gray-900">Projets utilisant cette technologie</h3></x-slot>
                @forelse($technologie->projets as $projet)
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0">
                        <a href="{{ route('projets.show', $projet->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                            {{ $projet->nom_projet }}
                        </a>
                        <span class="text-sm text-gray-500">{{ ucfirst($projet->statut_projet) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic py-4">Aucun projet associé à cette technologie.</p>
                @endforelse
            </x-card>
        </div>
    </div>
</x-app-layout>
