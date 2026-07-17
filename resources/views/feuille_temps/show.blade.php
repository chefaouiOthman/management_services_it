<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Feuille de Temps #{{ $feuille->id }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $feuille->employe?->user?->nom_complet ?? 'N/A' }} &mdash; {{ $feuille->projet?->nom_projet ?? 'N/A' }}</p>
            </div>
            <div class="flex gap-2">
                @if(auth()->user()->hasRole('Super Admin') || (auth()->user()->hasRole('Admin') && $feuille->created_by === auth()->id()))
                    <a href="{{ route('feuille_temps.edit', $feuille->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                        Modifier
                    </a>
                @endif
                <a href="{{ route('feuille_temps.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    &larr; Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Employé</p>
                        <p class="font-medium text-gray-900">{{ $feuille->employe?->user?->nom_complet ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Projet</p>
                        <p class="font-medium text-gray-900">{{ $feuille->projet?->nom_projet ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date d'effort</p>
                        <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($feuille->date_effort)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Durée</p>
                        <p class="font-medium text-gray-900">{{ $feuille->duree_heures }} h</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Commentaire</p>
                        <p class="font-medium text-gray-900">{{ $feuille->commentaire ?? 'Aucun commentaire' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500 mb-2">Tâches associées</p>
                        @forelse($feuille->taches as $tache)
                            <span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded text-sm border border-gray-200 mr-2 mb-2">
                                {{ $tache->titre_tache }}
                            </span>
                        @empty
                            <p class="text-sm text-gray-400 italic">Aucune tâche associée.</p>
                        @endforelse
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
