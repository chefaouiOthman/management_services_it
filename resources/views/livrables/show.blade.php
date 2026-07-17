<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $livrable->titre_jalon }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('livrables.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour aux livrables
                </a>
            </div>

            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Titre du Jalon</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $livrable->titre_jalon }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Projet associé</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $livrable->projet?->nom_projet ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Date Limite de Soumission</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $livrable->date_limite_soumission?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Statut Client</p>
                        @php $color = match($livrable->statut_client) { 'valide' => 'success', 'rejete_avec_corrections' => 'danger', default => 'warning' }; @endphp
                        <x-badge :type="$color" class="mt-1">{{ str_replace('_', ' ', $livrable->statut_client) }}</x-badge>
                    </div>
                    <div class="col-span-full">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Fichier joint</p>
                        @if($livrable->fichier_path)
                            <div class="mt-2 p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-between">
                                <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">{{ $livrable->fichier_nom_original ?? 'Document' }}</span>
                                <a href="{{ route('livrables.download', $livrable->id) }}" class="text-sm font-medium text-indigo-600 hover:underline">Télécharger</a>
                            </div>
                        @else
                            <p class="text-base text-gray-400 italic mt-1">Aucun fichier joint.</p>
                        @endif
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    @can('livrable-edit')
                    <a href="{{ route('livrables.edit', $livrable->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 transition">Modifier</a>
                    @endcan
                    @can('livrable-delete')
                    <form action="{{ route('livrables.destroy', $livrable->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer ce livrable ?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md font-medium hover:bg-red-700 transition">Supprimer</button>
                    </form>
                    @endcan
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
