<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pilotage Financier & Trésorerie
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ tab: window.location.hash ? (['notes-frais','fiches-paie'].includes(window.location.hash.substring(1)) ? 'rh' : window.location.hash.substring(1)) : 'dashboard' }" @hashchange.window="tab = (['notes-frais','fiches-paie'].includes(window.location.hash.substring(1)) ? 'rh' : window.location.hash.substring(1))">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs Master - Modern Cards (global totals from FinanceService) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Total Entrées --}}
                <div class="group relative bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-12 translate-x-12 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-white/80 text-sm font-semibold uppercase tracking-wider">Total Entrées</span>
                            <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                        <p class="text-4xl font-black text-white font-mono tracking-tight">+ {{ number_format($kpis['total_entrees'], 2, ',', ' ') }} DHS</p>
                        <p class="text-white/60 text-sm mt-2">Encaissements validés</p>
                    </div>
                </div>

                {{-- Total Sorties --}}
                <div class="group relative bg-gradient-to-br from-rose-500 to-rose-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-12 translate-x-12 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-white/80 text-sm font-semibold uppercase tracking-wider">Total Sorties</span>
                            <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </div>
                        <p class="text-4xl font-black text-white font-mono tracking-tight">- {{ number_format($kpis['total_sorties'], 2, ',', ' ') }} DHS</p>
                        <p class="text-white/60 text-sm mt-2">Paie & Frais décaissés</p>
                    </div>
                </div>

                {{-- Solde Net --}}
                <div class="group relative bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-12 translate-x-12 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-white/80 text-sm font-semibold uppercase tracking-wider">Solde de Trésorerie</span>
                            <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-4xl font-black text-white font-mono tracking-tight">{{ number_format($kpis['solde_net'], 2, ',', ' ') }} DHS</p>
                        <p class="text-white/60 text-sm mt-2">{{ $kpis['solde_net'] >= 0 ? 'Trésorerie positive' : 'Déficit' }}</p>
                    </div>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Évolution des Flux Financiers</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Entrées vs Sorties mensuelles ({{ date('Y') }})</p>
                    <div id="chart-evolution" class="w-full"></div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Répartition des Dépenses</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Masse Salariale vs Frais de Fonctionnement</p>
                    <div id="chart-depenses" class="w-full"></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Performance de Facturation</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Montant Facturé vs Encaissé par mois ({{ date('Y') }})</p>
                <div id="chart-facturation" class="w-full"></div>
            </div>

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 rounded-t-lg shadow-sm">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#dashboard" :class="tab === 'dashboard' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">Grand Livre (Flux)</a>
                    <a href="#facturation" :class="tab === 'facturation' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">Facturation Clients</a>
                    <a href="#rh" :class="tab === 'rh' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">Fiches de Paie & Notes de Frais</a>
                </nav>
            </div>

            <!-- TAB 1: GRAND LIVRE -->
            <div x-show="tab === 'dashboard'" x-cloak class="space-y-4">
                <x-search-filters :search="request('search')" searchPlaceholder="Rechercher par libellé, catégorie, montant..."
                    :filters="[
                        'type' => ['label' => 'Type', 'options' => ['entree' => 'Recette', 'sortie' => 'Dépense']],
                        'date_debut' => ['label' => 'Date début', 'type' => 'date'],
                        'date_fin' => ['label' => 'Date fin', 'type' => 'date'],
                    ]" />

                <x-card>
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Journal des Mouvements (Automatisé)</h3>
                        </div>
                    </x-slot>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                                <tr>
                                    <th class="px-6 py-3">Date Comptable</th>
                                    <th class="px-6 py-3">Catégorie</th>
                                    <th class="px-6 py-3">Pièce Justificative (Source)</th>
                                    <th class="px-6 py-3 text-right">Montant Opération</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($flux as $f)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $f->date_comptable?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $f->categorieFlux?->libelle_categorie ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            @if($f->facture)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-semibold">Facture #{{ $f->facture?->num_facture ?? 'N/A' }}</span>
                                            @elseif($f->fichePaie)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-semibold">Paie {{ $f->fichePaie?->mois_annee ?? 'N/A' }} - {{ $f->fichePaie?->employe?->user?->nom_complet ?? 'N/A' }}</span>
                                            @elseif($f->noteDeFrais)
                                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full font-semibold">NDF - {{ $f->noteDeFrais?->employe?->user?->nom_complet ?? 'N/A' }}</span>
                                            @else
                                                <span class="text-gray-400 italic">Mouvement Manuel</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-mono font-bold {{ $f->type_mouvement === 'entree' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $f->type_mouvement === 'entree' ? '+' : '-' }} {{ number_format($f->montant_operation, 2, ',', ' ') }} DHS
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            @can('flux-tresorerie-view')
                                            <a href="{{ route('flux_tresoreries.show', $f->id) }}" class="px-3 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs font-bold rounded transition shadow-sm">Voir</a>
                                            @endcan
                                            @can('flux-tresorerie-edit')
                                            @if($f->facture || $f->fichePaie || $f->noteDeFrais)
                                            <a href="{{ route('flux_tresoreries.edit', $f->id) }}" class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold rounded transition shadow-sm">Modifier</a>
                                            @else
                                            <span class="px-3 py-1 text-xs text-gray-400 italic" title="Flux manuel : aucune entité source modifiable">Source</span>
                                            @endif
                                            @endcan
                                            @can('flux-tresorerie-delete')
                                            <form action="{{ route('flux_tresoreries.destroy', $f->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Confirmez-vous la suppression de ce flux de trésorerie ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 text-xs font-bold rounded transition shadow-sm">Supprimer</button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Le grand livre est vide.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $flux->appends(request()->query())->links() }}
                </x-card>
            </div>

            <!-- TAB 2: FACTURATION -->
            <div x-show="tab === 'facturation'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Factures Clients</h3>
                    @can('facture-create')
                    <a href="{{ route('factures.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        + Créer Facture
                    </a>
                    @endcan
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @forelse($factures as $facture)
                        <x-card x-data="factureManager({{ $facture->id }}, '{{ $facture->statut_paiement }}')">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <h4 class="text-xl font-black text-gray-900">#{{ $facture->num_facture }}</h4>
                                    <p class="text-sm font-medium text-gray-600 mt-1">Client : {{ $facture->client?->user?->nom_complet ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Émise le {{ $facture->date_emission?->format('d/m/Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold font-mono text-gray-900">{{ number_format($facture->total_ttc, 2, ',', ' ') }} DHS</p>
                                    <div class="mt-2 flex items-center justify-end gap-2">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full uppercase"
                                              :class="{
                                                  'bg-gray-100 text-gray-700': currentStatut === 'emise',
                                                  'bg-red-100 text-red-700': currentStatut === 'en_retard_paiement',
                                                  'bg-green-100 text-green-700': currentStatut === 'soldee'
                                              }" x-text="currentStatut.replace(/_/g, ' ')"></span>
                                        
                                        @can('facture-view')
                                        <a :href="`/factures/${factureId}`" class="px-3 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs font-bold rounded transition shadow-sm">
                                            👁 Voir
                                        </a>
                                        @endcan
                                        
                                        @can('facture-edit')
                                        <a :href="`/factures/${factureId}/edit`" class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold rounded transition shadow-sm">
                                            ✎ Modifier
                                        </a>
                                        
                                        <!-- Bouton En Retard -->
                                        <button x-show="currentStatut === 'emise'" @click="changerStatut('en_retard_paiement')" class="px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded hover:bg-yellow-600 transition shadow-sm">
                                            En retard
                                        </button>

                                        <!-- Bouton Encaisser -->
                                        <button x-show="currentStatut !== 'soldee'" @click="changerStatut('soldee')" class="px-3 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700 transition shadow-sm">
                                            $ Encaisser
                                        </button>
                                        @endcan
                                        
                                        @can('facture-delete')
                                        <form :action="`/factures/${factureId}`" method="POST" class="inline-block" onsubmit="return confirm('Confirmez-vous la suppression de cette facture ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 text-xs font-bold rounded transition shadow-sm">
                                                ✗ Supprimer
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </x-card>
                    @empty
                        <x-card><p class="text-center text-gray-500 py-8">Aucune facture émise.</p></x-card>
                    @endforelse
                </div>
            </div>

            <!-- TAB 3: MASSE SALARIALE & FRAIS -->
            <div x-show="tab === 'rh'" x-cloak class="space-y-8">
                
                <!-- Sous-section : Fiches de Paie -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Fiches de Paie (Masse Salariale)</h3>
                        @can('fiche-paie-create')
                        <a href="{{ route('fiche_paies.create') }}" class="text-sm font-medium text-indigo-600 hover:underline">+ Générer Fiche</a>
                        @endcan
                    </div>
                    <x-card>
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Employé</th>
                                    <th class="px-6 py-3">Période</th>
                                    <th class="px-6 py-3 text-right">Net à Payer</th>
                                    <th class="px-6 py-3 text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fiches as $fiche)
                                    <tr class="border-b last:border-0 hover:bg-gray-50" x-data="paieManager({{ $fiche->id }}, {{ $fiche->flux_tresorerie_id ? 'true' : 'false' }})">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $fiche->employe?->user?->nom_complet ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 font-bold">{{ $fiche->mois_annee }}</td>
                                        <td class="px-6 py-4 text-right font-mono font-bold text-red-600">{{ number_format($fiche->net_a_payer, 2, ',', ' ') }} DHS</td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <template x-if="isPaid">
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-bold">Payée</span>
                                                </template>
                                                <template x-if="!isPaid">
                                                    @can('fiche-paie-edit')
                                                    <button @click="payer" class="px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded shadow hover:bg-indigo-700">Déclencher Paiement</button>
                                                    @else
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-bold">En attente</span>
                                                    @endcan
                                                </template>

                                                @can('fiche-paie-view')
                                                <a href="{{ route('fiche_paies.show', $fiche->id) }}" class="p-1 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded" title="Voir">👁</a>
                                                @endcan
                                                @can('fiche-paie-edit')
                                                <a href="{{ route('fiche_paies.edit', $fiche->id) }}" class="p-1 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded" title="Modifier">✎</a>
                                                @endcan
                                                @can('fiche-paie-delete')
                                                <form action="{{ route('fiche_paies.destroy', $fiche->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer définitivement cette fiche de paie ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1 bg-red-100 text-red-700 hover:bg-red-200 rounded" title="Supprimer">✗</button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucune fiche de paie.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </x-card>
                </div>

                <!-- Sous-section : Notes de Frais -->
                <div id="notes-frais">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Notes de Frais</h3>
                        @can('note-de-frais-create')
                        @role('Admin')
                        <!-- Modale Admin : Créer une Note de Frais -->
                        <div x-data="{ openFraisModal: false }">
                            <button @click="openFraisModal = true" class="text-sm font-medium text-indigo-600 hover:underline">
                                + Créer Note de Frais (Admin)
                            </button>
                            <div x-show="openFraisModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="openFraisModal = false">
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-lg mx-4">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Nouvelle Note de Frais</h3>
                                    <form action="{{ route('note_de_frais.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="statut_remboursement" value="soumis">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employé *</label>
                                            <select name="employe_id" required class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm">
                                                <option value="">-- Sélectionner un employé --</option>
                                                @foreach(\App\Models\Employe::with('user')->get() as $emp)
                                                    <option value="{{ $emp->user_id }}">{{ $emp->user?->nom_complet ?? 'N/A' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif de la dépense *</label>
                                            <input type="text" name="motif_depense" required class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Montant TTC *</label>
                                            <input type="number" step="0.01" name="montant_ttc" required class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Justificatif (PDF/Image) *</label>
                                            <input type="file" name="justificatif_fichier" required class="block w-full text-sm text-gray-500">
                                        </div>
                                        <div class="flex justify-end gap-3 pt-2">
                                            <button type="button" @click="openFraisModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">Annuler</button>
                                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold hover:bg-indigo-700">Soumettre</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                        <a href="{{ route('note_de_frais.create') }}" class="text-sm font-medium text-indigo-600 hover:underline">+ Soumettre Frais</a>
                        @endrole
                        @endcan
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        @forelse($notes as $note)
                            <x-card class="flex justify-between items-center" x-data="noteManager({{ $note->id }}, '{{ $note->statut_remboursement }}')">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 font-medium">{{ $note->employe?->user?->nom_complet ?? 'N/A' }}</p>
                                    <h4 class="text-lg font-bold text-gray-900">{{ $note->motif_depense }}</h4>
                                    <div class="mt-2 flex gap-3 items-center">
                                        <a href="{{ route('note_de_frais.download', $note->id) }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline bg-indigo-50 px-2 py-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            Pièce Jointe Privée
                                        </a>
                                        <span class="text-xs text-gray-400">{{ $note->created_at?->format('d/m/Y') ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="text-right flex flex-col items-end gap-2">
                                    <p class="text-xl font-bold font-mono text-gray-900">{{ number_format($note->montant_ttc, 2, ',', ' ') }} DHS</p>
                                    
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 text-[10px] font-bold rounded uppercase tracking-wider"
                                              :class="{
                                                  'bg-gray-200 text-gray-800': currentStatut === 'soumis',
                                                  'bg-blue-100 text-blue-800': currentStatut === 'approuve_manager',
                                                  'bg-red-100 text-red-800': currentStatut === 'rejete',
                                                  'bg-green-100 text-green-800': currentStatut === 'rembourse'
                                              }" x-text="currentStatut.replace('_', ' ')"></span>
                                        
                                        @can('note-de-frais-edit')
                                        <template x-if="currentStatut === 'soumis'">
                                            <div class="flex gap-1">
                                                <button @click="changerStatut('approuve_manager')" class="w-6 h-6 flex items-center justify-center bg-blue-100 text-blue-600 rounded hover:bg-blue-200" title="Approuver">✓</button>
                                                <button @click="changerStatut('rejete')" class="w-6 h-6 flex items-center justify-center bg-red-100 text-red-600 rounded hover:bg-red-200" title="Rejeter">✗</button>
                                            </div>
                                        </template>
                                        <template x-if="currentStatut === 'approuve_manager'">
                                            <button @click="changerStatut('rembourse')" class="px-2 py-1 text-xs bg-green-600 text-white rounded font-bold hover:bg-green-700 shadow-sm">$ Rembourser</button>
                                        </template>
                                        @endcan

                                        @can('note-de-frais-view')
                                        <a href="{{ route('note_de_frais.show', $note->id) }}" class="p-1 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded" title="Voir">👁</a>
                                        @endcan
                                        @can('note-de-frais-edit')
                                        <a href="{{ route('note_de_frais.edit', $note->id) }}" class="p-1 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded" title="Modifier">✎</a>
                                        @endcan
                                        @can('note-de-frais-delete')
                                        <form action="{{ route('note_de_frais.destroy', $note->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer définitivement cette note de frais ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 bg-red-100 text-red-700 hover:bg-red-200 rounded" title="Supprimer">✗</button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </x-card>
                        @empty
                            <x-card><p class="text-center text-gray-500 py-4">Aucune note de frais soumise.</p></x-card>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts Asynchrones Alpine.js -->
    <script>
        function factureManager(id, initialStatut) {
            return {
                factureId: id,
                currentStatut: initialStatut,
                changerStatut(nouveauStatut) {
                    let msg = "Confirmer le changement de statut ?";
                    if(nouveauStatut === 'soldee') msg = "Encaisser cette facture ? Cela générera un flux de trésorerie positif (Entrée) définitif.";
                    if(!confirm(msg)) return;
                    
                    fetch(`/factures/${this.factureId}/statut`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ statut_paiement: nouveauStatut })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.currentStatut = data.statut_paiement;
                            if(nouveauStatut === 'soldee') {
                                alert("Facture encaissée ! Le Grand Livre a été mis à jour.");
                                window.location.reload(); // Rafraîchir pour mettre à jour les KPIs master
                            } else {
                                alert(data.message || 'Statut mis à jour.');
                            }
                        } else {
                            alert(data.error || 'Erreur lors du changement de statut.');
                        }
                    }).catch(err => alert('Erreur réseau.'));
                }
            }
        }

        function paieManager(id, initialIsPaid) {
            return {
                id: id,
                isPaid: initialIsPaid,
                payer() {
                    if(!confirm("Valider le paiement de cette fiche de paie ? Cela générera un flux de trésorerie négatif (Sortie) définitif.")) return;
                    
                    fetch(`/fiche_paies/${this.id}/payer`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.isPaid = true;
                            alert("Fiche de paie réglée ! Le Grand Livre a été mis à jour.");
                            window.location.reload();
                        } else {
                            alert(data.error || 'Erreur lors du paiement.');
                        }
                    }).catch(err => alert('Erreur réseau.'));
                }
            }
        }

        function noteManager(id, initialStatut) {
            return {
                id: id,
                currentStatut: initialStatut,
                changerStatut(nouveauStatut) {
                    let msg = "Confirmer le changement de statut ?";
                    if(nouveauStatut === 'rembourse') msg = "Rembourser cette note ? Cela générera un flux de trésorerie négatif (Sortie) définitif.";
                    if(!confirm(msg)) return;

                    fetch(`/note_de_frais/${this.id}/statut`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ statut_remboursement: nouveauStatut })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.currentStatut = data.statut_remboursement;
                            if(nouveauStatut === 'rembourse') {
                                alert("Frais remboursés ! Le Grand Livre a été mis à jour.");
                                window.location.reload();
                            }
                        } else {
                            alert(data.error || 'Erreur lors du changement de statut.');
                        }
                    }).catch(err => alert('Erreur réseau.'));
                }
            }
        }
    </script>

    {{-- ApexCharts CDN & Chart Init --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var evolutionData = @json($evolution);
            var depensesData = @json($depenses);
            var facturationData = @json($facturation);

            var months = evolutionData.map(function (d) { return d.month; });

            // 1. Area Chart
            new ApexCharts(document.querySelector('#chart-evolution'), {
                chart: { type: 'area', height: 320, toolbar: { show: false }, animations: { initialAnimation: { enabled: true, speed: 800 } } },
                series: [
                    { name: 'Entrées', data: evolutionData.map(function (d) { return d.entrees; }), color: '#10B981' },
                    { name: 'Sorties', data: evolutionData.map(function (d) { return d.sorties; }), color: '#F43F5E' }
                ],
                xaxis: { categories: months, labels: { style: { colors: '#9CA3AF' } } },
                yaxis: { labels: { formatter: function (v) { return v.toLocaleString('fr-FR'); }, style: { colors: '#9CA3AF' } } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 90, 100] } },
                tooltip: { y: { formatter: function (v) { return v.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' DHS'; } } },
                grid: { borderColor: '#F3F4F6' },
                legend: { position: 'top', labels: { colors: '#6B7280' } }
            }).render();

            // 2. Donut Chart
            new ApexCharts(document.querySelector('#chart-depenses'), {
                chart: { type: 'donut', height: 320, animations: { initialAnimation: { enabled: true, speed: 800 } } },
                series: [depensesData.masse_salariale, depensesData.frais_fonctionnement],
                labels: ['Masse Salariale', 'Frais de Fonctionnement'],
                colors: ['#6366F1', '#F59E0B'],
                dataLabels: { enabled: true, formatter: function (v) { return v.toFixed(1) + '%'; }, style: { fontSize: '12px', fontWeight: 'bold' } },
                plotOptions: { pie: { donut: { size: '60%', labels: { show: true, total: { show: true, label: 'Total', formatter: function () { return (depensesData.masse_salariale + depensesData.frais_fonctionnement).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' DHS'; } } } } } },
                tooltip: { y: { formatter: function (v) { return v.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' DHS'; } } },
                legend: { position: 'bottom', labels: { colors: '#6B7280' } },
                responsive: [{ breakpoint: 480, options: { chart: { height: 280 } } }]
            }).render();

            // 3. Bar Chart
            new ApexCharts(document.querySelector('#chart-facturation'), {
                chart: { type: 'bar', height: 350, toolbar: { show: false }, animations: { initialAnimation: { enabled: true, speed: 800 } } },
                series: [
                    { name: 'Facturé', data: facturationData.map(function (d) { return d.facture; }), color: '#3B82F6' },
                    { name: 'Encaissé', data: facturationData.map(function (d) { return d.encaisse; }), color: '#10B981' }
                ],
                xaxis: { categories: facturationData.map(function (d) { return d.month; }), labels: { style: { colors: '#9CA3AF' } } },
                yaxis: { labels: { formatter: function (v) { return v.toLocaleString('fr-FR'); }, style: { colors: '#9CA3AF' } } },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                tooltip: { y: { formatter: function (v) { return v.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' DHS'; } } },
                grid: { borderColor: '#F3F4F6' },
                legend: { position: 'top', labels: { colors: '#6B7280' } },
                plotOptions: { bar: { borderRadius: 6, columnWidth: '60%' } }
            }).render();
        });
    </script>
</x-app-layout>
