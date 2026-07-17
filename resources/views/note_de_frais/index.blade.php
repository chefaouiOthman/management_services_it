<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Notes de Frais
            </h2>
            @can('note-de-frais-create')
            <a href="{{ route('note_de_frais.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Créer une note de frais
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

<x-search-filters :search="request('search')" searchPlaceholder="Rechercher par motif, employé, statut..."
    :filters="[
        'statut' => ['label' => 'Statut', 'options' => ['en_attente' => 'En attente', 'approuvée' => 'Approuvée', 'refusée' => 'Refusée', 'remboursée' => 'Remboursée']],
    ]" />

            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Employé</th>
                                <th class="px-6 py-3">Motif</th>
                                <th class="px-6 py-3">Montant TTC</th>
                                <th class="px-6 py-3">Statut</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notes as $note)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $note->employe?->user?->nom_complet ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">{{ $note->motif_depense }}</td>
                                    <td class="px-6 py-4 font-mono font-bold">{{ number_format($note->montant_ttc, 2, ',', ' ') }} DHS</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold uppercase
                                            @switch($note->statut_remboursement)
                                                @case('soumis') bg-gray-200 text-gray-800 @break
                                                @case('approuve_manager') bg-blue-100 text-blue-800 @break
                                                @case('rejete') bg-red-100 text-red-800 @break
                                                @case('rembourse') bg-green-100 text-green-800 @break
                                                @default bg-gray-100 text-gray-600
                                            @endswitch">
                                            {{ str_replace('_', ' ', $note->statut_remboursement) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('note_de_frais.show', $note->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs">Voir</a>
                                        <a href="{{ route('note_de_frais.download', $note->id) }}" class="text-green-600 hover:text-green-900 font-medium text-xs">Télécharger</a>
                                        @can('note-de-frais-edit')
                                        <a href="{{ route('note_de_frais.edit', $note->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Modifier</a>
                                        @endcan
                                        @can('note-de-frais-delete')
                                        <form action="{{ route('note_de_frais.destroy', $note->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette note de frais ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">Supprimer</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucune note de frais.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $notes->appends(request()->query())->links() }}
            </x-card>
        </div>
    </div>
</x-app-layout>
