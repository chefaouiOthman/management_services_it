<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Éditer Note de Frais
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('note_de_frais.update', $note->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="employe_id" value="Employé *" />
                            <select name="employe_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm">
                                <option value="">-- Sélectionner un employé --</option>
                                @foreach($employes as $employe)
                                    <option value="{{ $employe->user_id }}" @selected(old('employe_id', $note->employe_id) == $employe->user_id)>
                                        {{ $employe->user->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('employe_id')" />
                        </div>
                        
                        <div>
                            <x-input-label for="motif_depense" value="Motif de la dépense *" />
                            <x-text-input name="motif_depense" required class="mt-1 block w-full" :value="old('motif_depense', $note->motif_depense)" />
                            <x-input-error class="mt-2" :messages="$errors->get('motif_depense')" />
                        </div>
                        
                        <div>
                            <x-input-label for="montant_ttc" value="Montant TTC (DHS) *" />
                            <x-text-input type="number" step="0.01" name="montant_ttc" required class="mt-1 block w-full" :value="old('montant_ttc', $note->montant_ttc)" />
                            <x-input-error class="mt-2" :messages="$errors->get('montant_ttc')" />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-input-label for="justificatif_fichier" value="Remplacer le Justificatif (PDF, JPG, PNG) (Optionnel)" />
                            <div class="mt-1 flex items-center gap-4">
                                <input type="file" name="justificatif_fichier" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-gray-500 border border-gray-300 rounded-md p-2">
                                <a href="{{ route('note_de_frais.download', $note->id) }}" class="shrink-0 text-sm font-medium text-indigo-600 hover:underline">
                                    Voir justificatif actuel
                                </a>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('justificatif_fichier')" />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-input-label for="statut_remboursement" value="Statut de remboursement *" />
                            <select name="statut_remboursement" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm">
                                <option value="soumis" @selected(old('statut_remboursement', $note->statut_remboursement) == 'soumis')>Soumis</option>
                                <option value="approuve_manager" @selected(old('statut_remboursement', $note->statut_remboursement) == 'approuve_manager')>Approuvé Manager</option>
                                <option value="rejete" @selected(old('statut_remboursement', $note->statut_remboursement) == 'rejete')>Rejeté</option>
                                <option value="rembourse" @selected(old('statut_remboursement', $note->statut_remboursement) == 'rembourse')>Remboursé</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('statut_remboursement')" />
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('flux_tresoreries.index') }}#rh" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-xs uppercase rounded-md shadow-sm">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold text-xs uppercase rounded-md shadow-sm hover:bg-indigo-700">Mettre à jour</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
