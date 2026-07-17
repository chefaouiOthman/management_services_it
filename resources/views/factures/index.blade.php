<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Factures Clients
            </h2>
            <div class="flex gap-2">
                @can('facture-create')
                <a href="{{ route('factures.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    + Nouvelle Facture
                </a>
                @endcan
                <a href="{{ route('flux_tresoreries.index') }}#facturation" class="inline-flex items-center px-3 py-1.5 bg-gray-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-gray-700 transition">
                    ← Retour au Hub
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<x-search-filters :search="request('search')" searchPlaceholder="Rechercher par numéro, client, statut..."
    :filters="[
        'statut' => ['label' => 'Statut', 'options' => ['payée' => 'Payée', 'impayée' => 'Impayée', 'en_attente' => 'En attente', 'annulée' => 'Annulée']],
        'date_debut' => ['label' => 'Date début', 'type' => 'date'],
        'date_fin' => ['label' => 'Date fin', 'type' => 'date'],
    ]" />

            <div class="grid grid-cols-1 gap-4">
                @forelse($factures as $facture)
                    <x-card>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                <h4 class="text-xl font-black text-gray-900">#{{ $facture->num_facture }}</h4>
                                <p class="text-sm font-medium text-gray-600 mt-1">Client : {{ $facture->client?->user?->nom_complet ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400 mt-1">Émise le {{ $facture->date_emission?->format('d/m/Y') ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold font-mono text-gray-900">{{ number_format($facture->total_ttc, 2, ',', ' ') }} DHS</p>
                                <div class="mt-2 flex items-center justify-end gap-2">
                                    @php
                                        $statusColors = [
                                            'emise' => 'bg-gray-100 text-gray-700',
                                            'en_retard_paiement' => 'bg-red-100 text-red-700',
                                            'soldee' => 'bg-green-100 text-green-700',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-bold rounded-full uppercase {{ $statusColors[$facture->statut_paiement] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ str_replace('_', ' ', $facture->statut_paiement) }}
                                    </span>
                                    @can('facture-view')
                                    <a href="{{ route('factures.show', $facture->id) }}" class="px-3 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs font-bold rounded transition shadow-sm">Voir</a>
                                    @endcan
                                    @can('facture-edit')
                                    <a href="{{ route('factures.edit', $facture->id) }}" class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold rounded transition shadow-sm">Modifier</a>
                                    @endcan
                                    @can('facture-delete')
                                    <form action="{{ route('factures.destroy', $facture->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Confirmez-vous la suppression de cette facture ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 text-xs font-bold rounded transition shadow-sm">Supprimer</button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </x-card>
                @empty
                    <x-card>
                        <p class="text-center text-gray-500 py-8">Aucune facture émise.</p>
                    </x-card>
                @endforelse

                {{ $factures->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
