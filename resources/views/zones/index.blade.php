<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Zones de Sécurité') }}
            </h2>
            @can('zone-create')
            <a href="{{ route('zones.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Ajouter une Zone
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($zones->isEmpty())
                <x-card>
                    <p class="text-center text-gray-500 py-4">Aucune zone configurée.</p>
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($zones as $zone)
                        <x-card>
                            <x-slot name="header">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $zone->nom_salle }}</h3>
                                    @if($zone->est_active)
                                        <x-badge type="success">Active</x-badge>
                                    @else
                                        <x-badge type="danger">Désactivée</x-badge>
                                    @endif
                                </div>
                            </x-slot>

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Code Zone</span>
                                    <span class="font-mono font-medium text-gray-900 dark:text-gray-100">{{ $zone->code_zone }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Niveau requis</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">Niv. {{ $zone->niveau_requis }}</span>
                                </div>
                            </div>

                            <x-slot name="footer">
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('zones.show', $zone->id) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        Voir le journal →
                                    </a>
                                    <div class="flex space-x-2">
                                        @can('zone-edit')
                                        <a href="{{ route('zones.edit', $zone->id) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Modifier</a>
                                        @endcan
                                        @can('zone-delete')
                                        <form action="{{ route('zones.destroy', $zone->id) }}" method="POST" onsubmit="return confirm('Supprimer cette zone ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Supprimer</button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </x-slot>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
