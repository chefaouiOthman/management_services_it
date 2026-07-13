<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Fiches de Paie
            </h2>
            @can('fiche-paie-create')
            <a href="{{ route('fiche_paies.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Nouvelle Fiche de Paie
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Employé</th>
                                <th class="px-6 py-3">Période</th>
                                <th class="px-6 py-3">Net à Payer</th>
                                <th class="px-6 py-3">Statut</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fiches as $fiche)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $fiche->employe?->user?->nom_complet ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">{{ $fiche->mois_annee }}</td>
                                    <td class="px-6 py-4 font-mono font-bold">{{ number_format($fiche->net_a_payer, 2, ',', ' ') }} DHS</td>
                                    <td class="px-6 py-4">
                                        @if($fiche->flux_tresorerie_id)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Payée</span>
                                        @else
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">En attente</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('fiche_paies.show', $fiche->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs">Voir</a>
                                        @can('fiche-paie-edit')
                                        <a href="{{ route('fiche_paies.edit', $fiche->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Modifier</a>
                                        @endcan
                                        @can('fiche-paie-delete')
                                        <form action="{{ route('fiche_paies.destroy', $fiche->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette fiche de paie ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">Supprimer</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucune fiche de paie.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
