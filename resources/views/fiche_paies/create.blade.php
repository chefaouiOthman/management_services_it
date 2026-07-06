<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nouvelle Fiche de Paie
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('fiche_paies.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="employe_id" value="Employé *" />
                        <select name="employe_id" required class="mt-1 block w-full border-gray-300 rounded-md">
                            @foreach($employes as $employe)
                                <option value="{{ $employe->user_id }}">{{ $employe->user->nom_complet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="mois_annee" value="Mois/Année (MM/YYYY) *" />
                        <x-text-input name="mois_annee" required placeholder="07/2026" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="net_a_payer" value="Net à Payer (DHS) *" />
                        <x-text-input type="number" step="0.01" name="net_a_payer" required class="mt-1 block w-full" />
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('flux_tresoreries.index') }}#rh" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Enregistrer</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
