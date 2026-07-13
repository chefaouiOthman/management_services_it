<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Détail de l'Évaluation
            </h2>
            <div class="flex gap-2">
                @can('evaluation-edit')
                <a href="{{ route('evaluations.edit', $evaluation->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    Modifier
                </a>
                @endcan
                <a href="{{ route('evaluations.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Session & Intervenants</h3>
                </x-slot>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Session</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $evaluation->sessionFormation?->catalogueFormation?->titre_formation ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Évaluateur</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $evaluation->user?->nom_complet ?? 'Utilisateur inconnu' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Formateur noté</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $evaluation->employe?->user?->nom_complet ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-card>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Note Pédagogie</h3>
                    </x-slot>
                    <p class="text-4xl font-black text-indigo-600 dark:text-indigo-400">{{ $evaluation->note_pedagogie }}<span class="text-lg text-gray-500">/5</span></p>
                </x-card>
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Note Technique</h3>
                    </x-slot>
                    <p class="text-4xl font-black text-blue-600 dark:text-blue-400">{{ $evaluation->note_technique }}<span class="text-lg text-gray-500">/5</span></p>
                </x-card>
            </div>

            @if($evaluation->avis_textuel)
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Commentaire</h3>
                </x-slot>
                <p class="text-gray-700 dark:text-gray-300 italic">"{{ $evaluation->avis_textuel }}"</p>
            </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
