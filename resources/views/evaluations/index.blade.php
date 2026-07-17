<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    @if($session)
                        Évaluations — {{ $session->catalogueFormation?->titre_formation ?? 'N/A' }}
                    @else
                        Toutes les Évaluations
                    @endif
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    @if($session)
                        Session du {{ $session->date_debut?->format('d/m/Y') ?? 'N/A' }} au {{ $session->date_fin?->format('d/m/Y') ?? 'N/A' }}
                    @else
                        Vue globale de toutes les évaluations des sessions de formation.
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                @can('evaluation-create')
                <a href="{{ route('evaluations.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    + Créer une évaluation
                </a>
                @endcan
                @if($session)
                <a href="{{ route('sessions.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-gray-700 transition">
                    ← Retour
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Nombre d'évaluations</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $evaluations->count() }}</p>
                </x-card>
                @if($evaluations->count() > 0)
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Moy. Note Pédagogie</p>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                        {{ number_format($evaluations->avg('note_pedagogie'), 1) }}<span class="text-sm text-gray-500">/5</span>
                    </p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-500 font-medium">Moy. Note Technique</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                        {{ number_format($evaluations->avg('note_technique'), 1) }}<span class="text-sm text-gray-500">/5</span>
                    </p>
                </x-card>
                @endif
            </div>

<x-search-filters :search="request('search')" searchPlaceholder="Rechercher par stagiaire, note..."
    :filters="[
        'note_min' => ['label' => 'Note technique min', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5']],
    ]" />

            <!-- Liste des évaluations -->
            @if($evaluations->isEmpty())
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucune évaluation</h3>
                    <p class="mt-1 text-sm text-gray-500">Aucune évaluation n'a encore été soumise pour cette session.</p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($evaluations as $evaluation)
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                            <!-- En-tête : Évaluateur et formateur évalué -->
                            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                @if(!$session)
                                    <div class="mb-3 pb-3 border-b border-gray-200 dark:border-gray-600">
                                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Session de Formation</p>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $evaluation->sessionFormation?->catalogueFormation?->titre_formation ?? 'Session supprimée' }}</p>
                                    </div>
                                @endif
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Soumis par</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $evaluation->user?->nom_complet ?? 'Utilisateur inconnu' }}</p>
                                        <p class="text-xs text-gray-500">{{ $evaluation->user?->email ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Formateur noté</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $evaluation->formateur?->user?->nom_complet ?? 'Formateur inconnu' }}</p>
                                        <p class="text-xs text-gray-500">{{ $evaluation->formateur?->user?->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="px-6 py-4 flex gap-4">
                                <div class="flex-1 text-center p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg border border-indigo-100 dark:border-indigo-800">
                                    <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Pédagogie</p>
                                    <p class="text-3xl font-black text-indigo-700 dark:text-indigo-300">{{ $evaluation->note_pedagogie }}</p>
                                    <p class="text-xs text-indigo-500">/5</p>
                                </div>
                                <div class="flex-1 text-center p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-100 dark:border-blue-800">
                                    <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Technique</p>
                                    <p class="text-3xl font-black text-blue-700 dark:text-blue-300">{{ $evaluation->note_technique }}</p>
                                    <p class="text-xs text-blue-500">/5</p>
                                </div>
                            </div>

                            <!-- Avis textuel -->
                            @if($evaluation->avis_textuel)
                                <div class="px-6 pb-4">
                                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Commentaire</p>
                                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 italic">
                                        "{{ $evaluation->avis_textuel }}"
                                    </div>
                                </div>
                            @endif

                            <div class="px-6 pb-4 flex justify-between items-center">
                                <span class="text-xs text-gray-400">{{ $evaluation->created_at?->format('d/m/Y') ?? 'N/A' }}</span>
                                <div class="flex gap-2">
                                    @can('evaluation-edit')
                                    <a href="{{ route('evaluations.edit', $evaluation->id) }}" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">Modifier</a>
                                    @endcan
                                    @can('evaluation-delete')
                                    <form action="{{ route('evaluations.destroy', $evaluation->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette évaluation ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-900 font-medium">Supprimer</button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
