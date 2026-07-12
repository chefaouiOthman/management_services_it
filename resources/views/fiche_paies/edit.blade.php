<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Éditer Fiche de Paie
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('fiche_paies.update', $fiche->id) }}" class="space-y-6">
                @csrf
                @method('PUT')
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="employe_id" value="Employé *" />
                            <select id="employe_id" name="employe_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm" required>
                                <option value="">-- Sélectionner un employé --</option>
                                @foreach($employes as $emp)
                                    <option value="{{ $emp->user_id }}" @selected(old('employe_id', $fiche->employe_id) == $emp->user_id)>
                                        {{ $emp->user->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('employe_id')" />
                        </div>

                        <div>
                            <x-input-label for="mois_annee" value="Mois/Année (MM/YYYY) *" />
                            <x-text-input id="mois_annee" name="mois_annee" type="text" class="mt-1 block w-full" :value="old('mois_annee', $fiche->mois_annee)" required placeholder="05/2023" />
                            <x-input-error class="mt-2" :messages="$errors->get('mois_annee')" />
                        </div>

                        <div>
                            <x-input-label for="net_a_payer" value="Net à Payer (DHS) *" />
                            <x-text-input id="net_a_payer" name="net_a_payer" type="number" step="0.01" class="mt-1 block w-full font-mono" :value="old('net_a_payer', $fiche->net_a_payer)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('net_a_payer')" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('flux_tresoreries.index') }}#rh" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                            Mettre à jour
                        </button>
                    </div>
                </x-card>
            </form>
        </div>
    </div>
</x-app-layout>
