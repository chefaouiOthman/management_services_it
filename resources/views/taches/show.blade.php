<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Détails de la Tâche : {{ $tache->titre_tache }}
            </h2>
            <a href="{{ route('taches.index') }}" class="text-indigo-600 hover:underline text-sm">&larr; Retour aux tâches</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Informations</h3>
                </x-slot>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Titre</p>
                        <p class="text-base font-semibold text-gray-900">{{ $tache->titre_tache }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Créée le</p>
                        <p class="text-base font-semibold text-gray-900">{{ $tache->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                </div>
            </x-card>

            @if($tache->projets?->isNotEmpty() ?? false)
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Projets liés</h3>
                </x-slot>
                <ul class="divide-y">
                    @foreach($tache->projets as $projet)
                    <li class="py-3 flex justify-between items-center">
                        <a href="{{ route('projets.show', $projet->id) }}" class="text-indigo-600 hover:underline font-medium">{{ $projet->titre_projet }}</a>
                        <div class="flex gap-2 text-xs">
                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-700">{{ $projet->pivot?->priorite ?? '-' }}</span>
                            <span class="px-2 py-1 rounded {{ ($projet->pivot?->statut_tache ?? '') === 'termine' ? 'bg-green-100 text-green-800' : (($projet->pivot?->statut_tache ?? '') === 'en_cours' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700') }}">{{ $projet->pivot?->statut_tache ?? '-' }}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </x-card>
            @endif

            @if($tache->feuilleTemps?->isNotEmpty() ?? false)
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Feuilles de temps associées</h3>
                </x-slot>
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Durée</th>
                            <th class="px-4 py-2">Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tache->feuilleTemps as $ft)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $ft->date_effort?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $ft->duree_heures }} h</td>
                            <td class="px-4 py-2">{{ $ft->commentaire ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
            @endif

            <div class="flex justify-end gap-3">
                @can('tache-edit')
                <a href="{{ route('taches.edit', $tache->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold hover:bg-indigo-700">Modifier</a>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
