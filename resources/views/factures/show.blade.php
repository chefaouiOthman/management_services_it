<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Détails de la Facture : ') }} {{ $facture->num_facture }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('flux_tresoreries.index') }}#facturation" class="inline-flex items-center px-3 py-1.5 bg-gray-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-gray-700 transition">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                <!-- En-tête de la facture -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">FACTURE</h3>
                        <p class="text-sm font-medium text-gray-500">N° {{ $facture->num_facture }}</p>
                        <p class="text-sm font-medium text-gray-500">Date d'émission : {{ $facture->date_emission?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        @php
                            $statusColors = [
                                'emise' => 'bg-gray-100 text-gray-800 border-gray-300',
                                'en_retard_paiement' => 'bg-red-100 text-red-800 border-red-300',
                                'soldee' => 'bg-green-100 text-green-800 border-green-300',
                            ];
                            $colorClass = $statusColors[$facture->statut_paiement] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wide border {{ $colorClass }}">
                            {{ str_replace('_', ' ', $facture->statut_paiement) }}
                        </span>
                    </div>
                </div>

                <!-- Informations Client -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Facturé à</h4>
                    <p class="font-bold text-gray-900 dark:text-white text-lg">{{ $facture->client?->user?->nom_complet ?? 'Inconnu' }}</p>
                    @if($facture->client?->type_client === 'morale')
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Société : {{ $facture->client?->nom_societe ?? 'N/A' }}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">ICE : {{ $facture->client?->ice ?? 'N/A' }}</p>
                    @endif
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $facture->client?->user?->email ?? 'N/A' }}</p>
                </div>

                <!-- Lignes de Facture -->
                <div class="p-6">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Détail des prestations</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-4 py-3">Désignation</th>
                                    <th class="px-4 py-3 text-center">Quantité</th>
                                    <th class="px-4 py-3 text-right">Prix Unitaire HT</th>
                                    <th class="px-4 py-3 text-right">TVA</th>
                                    <th class="px-4 py-3 text-right">Total TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalHT = 0;
                                    $totalTVA = 0;
                                    $totalTTC = 0;
                                @endphp
                                @forelse($facture->ligneFactures as $ligne)
                                    @php
                                        $ligneHT = $ligne->quantite * $ligne->prix_unitaire_ht;
                                        $ligneTVA = $ligneHT * ($ligne->taux_tva / 100);
                                        $ligneTTC = $ligneHT + $ligneTVA;
                                        
                                        $totalHT += $ligneHT;
                                        $totalTVA += $ligneTVA;
                                        $totalTTC += $ligneTTC;
                                    @endphp
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $ligne->designation }}</td>
                                        <td class="px-4 py-3 text-center">{{ $ligne->quantite }}</td>
                                        <td class="px-4 py-3 text-right">{{ number_format($ligne->prix_unitaire_ht, 2, ',', ' ') }} DHS</td>
                                        <td class="px-4 py-3 text-right">{{ $ligne->taux_tva }}%</td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">{{ number_format($ligneTTC, 2, ',', ' ') }} DHS</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500 italic">Aucune ligne facturée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totaux -->
                <div class="p-6 bg-gray-50 dark:bg-gray-900/50 flex justify-end">
                    <div class="w-full md:w-1/2 lg:w-1/3 space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Total HT :</span>
                            <span>{{ number_format($totalHT, 2, ',', ' ') }} DHS</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Total TVA :</span>
                            <span>{{ number_format($totalTVA, 2, ',', ' ') }} DHS</span>
                        </div>
                        <div class="flex justify-between text-lg font-black text-gray-900 dark:text-white pt-2 border-t border-gray-300 dark:border-gray-600">
                            <span>Total TTC :</span>
                            <span>{{ number_format($totalTTC, 2, ',', ' ') }} DHS</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flux Trésorerie Associé -->
            @if($facture->fluxTresorerie)
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-indigo-800 dark:text-indigo-300 uppercase tracking-wider">Paiement Encaissé</p>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1">Le flux de trésorerie a été généré le {{ $facture->fluxTresorerie?->date_comptable?->format('d/m/Y à H:i') ?? 'N/A' }}.</p>
                    </div>
                    <div class="text-indigo-700 dark:text-indigo-400 text-2xl font-black font-mono">
                        + {{ number_format($facture->fluxTresorerie?->montant_operation ?? 0, 2, ',', ' ') }} DHS
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
