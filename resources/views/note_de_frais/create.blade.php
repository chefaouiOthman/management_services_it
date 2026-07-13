<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Soumettre une Note de Frais
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('note_de_frais.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="employe_id" value="Employé *" />
                        <select name="employe_id" required class="mt-1 block w-full border-gray-300 rounded-md">
                            @foreach($employes as $employe)
                                <option value="{{ $employe->user_id }}">{{ $employe->user?->nom_complet ?? 'Sans nom' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="motif_depense" value="Motif de la dépense *" />
                        <x-text-input name="motif_depense" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="montant_ttc" value="Montant TTC (DHS) *" />
                        <x-text-input type="number" step="0.01" name="montant_ttc" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="justificatif_fichier" value="Justificatif (PDF, JPG, PNG) *" />
                        <input type="file" name="justificatif_fichier" accept=".pdf,.jpg,.jpeg,.png" required class="mt-1 block w-full text-sm text-gray-500 border border-gray-300 rounded-md p-2">
                    </div>
                    
                    <input type="hidden" name="statut_remboursement" value="soumis">
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('flux_tresoreries.index') }}#rh" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Soumettre</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
