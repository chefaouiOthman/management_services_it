<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nouvelle Facture
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <form action="{{ route('factures.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="client_id" value="Client *" />
                            <select name="client_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm">
                                <option value="">-- Sélectionner un client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->user_id }}" @selected(old('client_id') == $client->user_id)>
                                        {{ $client->user?->nom_complet ?? 'Client sans compte' }}
                                        @if($client->nom_societe) ({{ $client->nom_societe }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('client_id')" />
                        </div>
                        <div>
                            <x-input-label for="num_facture" value="Numéro de Facture *" />
                            <x-text-input name="num_facture" required class="mt-1 block w-full" :value="old('num_facture')" placeholder="FAC-2026-001" />
                            <x-input-error class="mt-2" :messages="$errors->get('num_facture')" />
                        </div>
                        <div>
                            <x-input-label for="date_emission" value="Date d'Émission *" />
                            <x-text-input type="date" name="date_emission" required class="mt-1 block w-full" :value="old('date_emission', date('Y-m-d'))" />
                            <x-input-error class="mt-2" :messages="$errors->get('date_emission')" />
                        </div>
                        <div>
                            <x-input-label for="statut_paiement" value="Statut *" />
                            <select name="statut_paiement" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm">
                                <option value="emise" @selected(old('statut_paiement') === 'emise')>Émise</option>
                                <option value="en_retard_paiement" @selected(old('statut_paiement') === 'en_retard_paiement')>En Retard de Paiement</option>
                                <option value="soldee" @selected(old('statut_paiement') === 'soldee')>Soldée</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('statut_paiement')" />
                        </div>
                    </div>

                    <hr class="my-2 border-gray-200">
                    <h3 class="text-base font-bold text-gray-900">Lignes de Facturation</h3>
                    <div id="lignes-container" class="space-y-3">
                        <div class="grid grid-cols-12 gap-2 text-xs font-bold text-gray-500 uppercase px-1 mb-1">
                            <div class="col-span-5">Désignation</div>
                            <div class="col-span-2">Qté</div>
                            <div class="col-span-2">Prix HT (DHS)</div>
                            <div class="col-span-2">TVA %</div>
                            <div class="col-span-1"></div>
                        </div>
                        <div class="ligne-facture grid grid-cols-12 gap-2 items-center">
                            <input name="lignes[0][designation]" placeholder="Description du service" required class="col-span-5 border-gray-300 rounded-md text-sm shadow-sm" />
                            <input name="lignes[0][quantite]" type="number" step="0.01" min="0" value="1" required class="col-span-2 border-gray-300 rounded-md text-sm shadow-sm" />
                            <input name="lignes[0][prix_unitaire_ht]" type="number" step="0.01" min="0" required class="col-span-2 border-gray-300 rounded-md text-sm shadow-sm" />
                            <input name="lignes[0][taux_tva]" type="number" step="0.01" min="0" max="100" value="20" required class="col-span-2 border-gray-300 rounded-md text-sm shadow-sm" />
                            <button type="button" class="col-span-1 text-red-500 hover:text-red-700 font-bold" onclick="this.closest('.ligne-facture').remove()">✕</button>
                        </div>
                    </div>
                    <button type="button" id="btn-ajouter-ligne" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-semibold">+ Ajouter une ligne</button>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('flux_tresoreries.index') }}#facturation" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 transition">Annuler</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 transition">Émettre la Facture</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        let ligneIndex = 1;
        document.getElementById('btn-ajouter-ligne').addEventListener('click', function () {
            const container = document.getElementById('lignes-container');
            const div = document.createElement('div');
            div.className = 'ligne-facture grid grid-cols-12 gap-2 items-center';
            div.innerHTML = `
                <input name="lignes[${ligneIndex}][designation]" placeholder="Description" required class="col-span-5 border-gray-300 rounded-md text-sm shadow-sm" />
                <input name="lignes[${ligneIndex}][quantite]" type="number" step="0.01" min="0" value="1" required class="col-span-2 border-gray-300 rounded-md text-sm shadow-sm" />
                <input name="lignes[${ligneIndex}][prix_unitaire_ht]" type="number" step="0.01" min="0" required class="col-span-2 border-gray-300 rounded-md text-sm shadow-sm" />
                <input name="lignes[${ligneIndex}][taux_tva]" type="number" step="0.01" value="20" min="0" max="100" required class="col-span-2 border-gray-300 rounded-md text-sm shadow-sm" />
                <button type="button" class="col-span-1 text-red-500 hover:text-red-700 font-bold" onclick="this.closest('.ligne-facture').remove()">✕</button>
            `;
            container.appendChild(div);
            ligneIndex++;
        });
    </script>
</x-app-layout>
