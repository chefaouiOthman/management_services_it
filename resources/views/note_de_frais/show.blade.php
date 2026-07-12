<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Détails Note de Frais
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
                        <p class="mt-1 text-lg text-gray-900 dark:text-white font-medium">{{ $note->employe->user->nom_complet }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase">Motif de la dépense</p>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white font-medium">{{ $note->motif_depense }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase">Montant TTC</p>
                        <p class="mt-1 text-2xl font-bold font-mono text-gray-900 dark:text-white">{{ number_format($note->montant_ttc, 2, ',', ' ') }} DHS</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase">Statut</p>
                        <div class="mt-1">
                            @php
                                $statusColors = [
                                    'soumis' => 'bg-gray-200 text-gray-800',
                                    'approuve_manager' => 'bg-blue-100 text-blue-800',
                                    'rejete' => 'bg-red-100 text-red-800',
                                    'rembourse' => 'bg-green-100 text-green-800',
                                ];
                                $statusClass = $statusColors[$note->statut_remboursement] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-3 py-1 rounded-full font-bold text-sm uppercase tracking-wider {{ $statusClass }}">
                                {{ str_replace('_', ' ', $note->statut_remboursement) }}
                            </span>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-bold text-gray-500 uppercase mb-2">Justificatif (Pièce Jointe Privée)</p>
                        <a href="{{ route('note_de_frais.download', $note->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-md hover:bg-indigo-100 transition shadow-sm font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Télécharger le justificatif sécurisé
                        </a>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    @can('note-de-frais-edit')
                        <a href="{{ route('note_de_frais.edit', $note->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold text-xs rounded transition shadow-sm uppercase">
                            Modifier
                        </a>
                    @endcan
                    @can('note-de-frais-delete')
                        <form action="{{ route('note_de_frais.destroy', $note->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer définitivement cette note de frais ?');">
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
