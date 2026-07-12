<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Détails Fiche de Paie
            </h2>
            <a href="{{ route('flux_tresoreries.index') }}#rh" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                ← Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase">Employé</p>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white font-medium">{{ $fiche->employe->user->nom_complet }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase">Période</p>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white font-medium">{{ $fiche->mois_annee }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase">Net à Payer</p>
                        <p class="mt-1 text-2xl font-bold font-mono text-red-600">{{ number_format($fiche->net_a_payer, 2, ',', ' ') }} DHS</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase">Statut</p>
                        <div class="mt-1">
                            @if($fiche->flux_tresorerie_id)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-bold text-sm">Payée</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full font-bold text-sm">En attente</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    @can('fiche-paie-edit')
                        <a href="{{ route('fiche_paies.edit', $fiche->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold text-xs rounded transition shadow-sm uppercase">
                            Modifier
                        </a>
                    @endcan
                    @can('fiche-paie-delete')
                        <form action="{{ route('fiche_paies.destroy', $fiche->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer définitivement cette fiche de paie ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 font-bold text-xs rounded transition shadow-sm uppercase">
                                Supprimer
                            </button>
                        </form>
                    @endcan
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
