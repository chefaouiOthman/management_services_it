<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pilotage Financier & Trésorerie
        </h2>
    </x-slot>

    @php
        $totalEntrees = $flux->where('type_mouvement', 'entree')->sum('montant_operation');
        $totalSorties = $flux->where('type_mouvement', 'sortie')->sum('montant_operation');
        $soldeNet = $totalEntrees - $totalSorties;
    @endphp

    <div class="py-12" x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'dashboard' }" @hashchange.window="tab = window.location.hash.substring(1)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs Master -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-card class="border-l-4 border-green-500 bg-green-50 dark:bg-green-900/20">
                    <p class="text-sm text-green-700 dark:text-green-400 font-bold uppercase tracking-wider">Total Entrées (Factures)</p>
                    <p class="text-3xl font-black text-green-800 dark:text-green-300 mt-2 font-mono">+ {{ number_format($totalEntrees, 2, ',', ' ') }} DHS</p>
                </x-card>
                <x-card class="border-l-4 border-red-500 bg-red-50 dark:bg-red-900/20">
                    <p class="text-sm text-red-700 dark:text-red-400 font-bold uppercase tracking-wider">Total Sorties (Paie & Frais)</p>
                    <p class="text-3xl font-black text-red-800 dark:text-red-300 mt-2 font-mono">- {{ number_format($totalSorties, 2, ',', ' ') }} DHS</p>
                </x-card>
                <x-card class="border-l-4 {{ $soldeNet >= 0 ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-red-600 bg-red-100' }}">
                    <p class="text-sm {{ $soldeNet >= 0 ? 'text-indigo-700 dark:text-indigo-400' : 'text-red-800' }} font-bold uppercase tracking-wider">Solde de Trésorerie Actuel</p>
                    <p class="text-3xl font-black {{ $soldeNet >= 0 ? 'text-indigo-800 dark:text-indigo-300' : 'text-red-900' }} mt-2 font-mono">
                        {{ number_format($soldeNet, 2, ',', ' ') }} DHS
                    </p>
                </x-card>
            </div>

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 rounded-t-lg shadow-sm">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#dashboard" :class="tab === 'dashboard' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">Grand Livre (Flux)</a>
                    <a href="#facturation" :class="tab === 'facturation' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">Facturation Clients</a>
                    <a href="#rh" :class="tab === 'rh' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">Masse Salariale & Notes de Frais</a>
                </nav>
            </div>

            <!-- TAB 1: GRAND LIVRE -->
            <div x-show="tab === 'dashboard'" x-cloak class="space-y-4">
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
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $f->date_comptable->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4">{{ $f->categorieFlux->libelle_categorie }}</td>
                                        <td class="px-6 py-4">
                                            @if($f->facture)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-semibold">Facture #{{ $f->facture->num_facture }}</span>
                                            @elseif($f->fichePaie)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-semibold">Paie {{ $f->fichePaie->mois_annee }} - {{ $f->fichePaie->employe->user->nom_complet }}</span>
                                            @elseif($f->noteDeFrais)
                                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full font-semibold">NDF - {{ $f->noteDeFrais->employe->user->nom_complet }}</span>
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
                                            <a href="{{ route('flux_tresoreries.edit', $f->id) }}" class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold rounded transition shadow-sm">Modifier</a>
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
                                    <p class="text-sm font-medium text-gray-600 mt-1">Client : {{ $facture->client->user->nom_complet }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Émise le {{ $facture->date_emission->format('d/m/Y') }}</p>
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
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $fiche->employe->user->nom_complet }}</td>
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
                <div>
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
                                                    <option value="{{ $emp->user_id }}">{{ $emp->user->nom_complet }}</option>
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
                                    <p class="text-sm text-gray-500 font-medium">{{ $note->employe->user->nom_complet }}</p>
                                    <h4 class="text-lg font-bold text-gray-900">{{ $note->motif_depense }}</h4>
                                    <div class="mt-2 flex gap-3 items-center">
                                        <a href="{{ route('note_de_frais.download', $note->id) }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline bg-indigo-50 px-2 py-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            Pièce Jointe Privée
                                        </a>
                                        <span class="text-xs text-gray-400">{{ $note->created_at->format('d/m/Y') }}</span>
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
</x-app-layout>
