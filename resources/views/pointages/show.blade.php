<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Détail du Pointage</h2>
            <div class="flex gap-2">
                @can('pointage-edit')
                <a href="{{ route('pointages.edit', $pointage->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Modifier</a>
                @endcan
                <a href="{{ route('pointages.index') }}" class="text-indigo-600 hover:underline text-sm">&larr; Retour</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $pointage->user?->nom_complet ?? 'Inconnu' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($pointage->date_jour)->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Heure d'arrivée</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $pointage->heure_arrivee ? \Carbon\Carbon::parse($pointage->heure_arrivee)->format('H:i:s') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Heure de départ</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $pointage->heure_depart ? \Carbon\Carbon::parse($pointage->heure_depart)->format('H:i:s') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</dt>
                        <dd class="mt-1">
                            <span class="px-3 py-1 text-sm font-bold rounded-full uppercase
                                {{ $pointage->statut_presence === 'a_l_heure' ? 'bg-green-100 text-green-800' : 
                                  ($pointage->statut_presence === 'en_retard' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ str_replace('_', ' ', $pointage->statut_presence) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Créé par</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $pointage->creator?->nom_complet ?? 'Système' }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>
</x-app-layout>
