<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Détails de l'Historique de Passage
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('zones.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour aux zones
                </a>
            </div>

            <x-card>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Utilisateur</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $historique->user->nom_complet ?? 'Inconnu' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Zone</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $historique->zone->nom_salle ?? 'Zone inconnue' }} ({{ $historique->zone->code_zone ?? 'N/A' }})</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Horodatage</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($historique->horodatage)->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Statut</p>
                            @if($historique->tentative_statut == 'autorise')
                                <x-badge type="success">Autorisé</x-badge>
                            @elseif($historique->tentative_statut == 'refuse_niveau_insuffisant')
                                <x-badge type="warning">Niveau Insuffisant</x-badge>
                            @elseif($historique->tentative_statut == 'refuse_zone_desactivee')
                                <x-badge type="danger">Zone Désactivée</x-badge>
                            @else
                                <x-badge type="gray">{{ $historique->tentative_statut }}</x-badge>
                            @endif
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex gap-3">
                            @can('historique-passage-edit')
                                <a href="{{ route('historique_passages.edit', $historique->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                                    Modifier
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
