<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Détails du Flux de Trésorerie #{{ $flux->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('flux_tresoreries.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour au Grand Livre
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Flux Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Informations du Flux</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Date Comptable</p>
                                <p class="text-lg font-semibold">{{ $flux->date_comptable->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Type de Mouvement</p>
                                <p class="text-lg font-semibold">
                                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $flux->type_mouvement === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $flux->type_mouvement === 'entree' ? 'Entrée' : 'Sortie' }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Montant Opération</p>
                                <p class="text-2xl font-bold font-mono {{ $flux->type_mouvement === 'entree' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $flux->type_mouvement === 'entree' ? '+' : '-' }} {{ number_format($flux->montant_operation, 2, ',', ' ') }} DHS
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Date de Création</p>
                                <p class="text-lg font-semibold">{{ $flux->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Category Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Catégorie de Flux</h3>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Libellé Catégorie</p>
                                    <p class="text-lg font-semibold">{{ $flux->categorieFlux->libelle_categorie }}</p>
                                </div>
                                @if($flux->categorieFlux->code_comptable)
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Code Comptable</p>
                                    <p class="text-lg font-semibold font-mono">{{ $flux->categorieFlux->code_comptable }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Source Document Information -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Document Source</h3>
                        @if($flux->facture)
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                                <p class="text-sm text-green-600 dark:text-green-400 font-semibold mb-2">Facture Client</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Numéro Facture</p>
                                        <p class="font-semibold">{{ $flux->facture->num_facture }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Client</p>
                                        <p class="font-semibold">{{ $flux->facture->client?->user?->nom_complet ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Date Émission</p>
                                        <p class="font-semibold">{{ $flux->facture->date_emission->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('factures.show', $flux->facture->id) }}" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition">
                                        Voir la Facture
                                    </a>
                                </div>
                            </div>
                        @elseif($flux->fichePaie)
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-600 dark:text-blue-400 font-semibold mb-2">Fiche de Paie</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Employé</p>
                                        <p class="font-semibold">{{ $flux->fichePaie->employe?->user?->nom_complet ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Période</p>
                                        <p class="font-semibold">{{ $flux->fichePaie->mois_annee }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Net à Payer</p>
                                        <p class="font-semibold font-mono">{{ number_format($flux->fichePaie->net_a_payer, 2, ',', ' ') }} DHS</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('fiche_paies.show', $flux->fichePaie->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                                        Voir la Fiche de Paie
                                    </a>
                                </div>
                            </div>
                        @elseif($flux->noteDeFrais)
                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                                <p class="text-sm text-purple-600 dark:text-purple-400 font-semibold mb-2">Note de Frais</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Employé</p>
                                        <p class="font-semibold">{{ $flux->noteDeFrais->employe?->user?->nom_complet ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Motif</p>
                                        <p class="font-semibold">{{ $flux->noteDeFrais->motif_depense }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Montant TTC</p>
                                        <p class="font-semibold font-mono">{{ number_format($flux->noteDeFrais->montant_ttc, 2, ',', ' ') }} DHS</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('note_de_frais.show', $flux->noteDeFrais->id) }}" class="inline-flex items-center px-3 py-1 bg-purple-600 text-white text-sm font-medium rounded hover:bg-purple-700 transition">
                                        Voir la Note de Frais
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <p class="text-sm text-gray-600 dark:text-gray-400 italic">Mouvement manuel sans document source associé</p>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                        @can('flux-tresorerie-edit')
                        <a href="{{ route('flux_tresoreries.edit', $flux->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 transition">
                            Modifier
                        </a>
                        @endcan
                        @can('flux-tresorerie-delete')
                        <form action="{{ route('flux_tresoreries.destroy', $flux->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Confirmez-vous la suppression de ce flux de trésorerie ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md font-medium hover:bg-red-700 transition">
                                Supprimer
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
