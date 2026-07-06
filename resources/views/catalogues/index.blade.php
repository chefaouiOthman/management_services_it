<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Catalogue des Formations
            </h2>
            @can('catalogue-formation-create')
            <a href="{{ route('catalogue.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Nouveau Programme
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($catalogues as $catalogue)
                    <x-card class="flex flex-col h-full">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $catalogue->titre_formation }}</h3>
                            <span class="text-lg font-mono text-indigo-600 dark:text-indigo-400 font-bold">{{ number_format($catalogue->prix_standard, 0, ',', ' ') }} DHS</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3 flex-1">
                            {{ $catalogue->description_programme }}
                        </p>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-1 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                {{ $catalogue->supportCours->count() }} Supports
                            </div>
                            <div class="space-x-2">
                                <a href="{{ route('catalogue.show', $catalogue->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium text-sm">Ouvrir</a>
                                @can('catalogue-formation-edit')
                                <a href="{{ route('catalogue.edit', $catalogue->id) }}" class="text-gray-500 hover:text-gray-900 font-medium text-sm">Éditer</a>
                                @endcan
                            </div>
                        </div>
                    </x-card>
                @empty
                    <div class="col-span-full py-8 text-center text-gray-500">
                        Aucun programme de formation dans le catalogue.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
