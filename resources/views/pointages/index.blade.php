<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historique des Pointages
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Employé</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Arrivée</th>
                            <th class="px-6 py-3">Départ</th>
                            <th class="px-6 py-3">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pointages as $pointage)
                            <tr class="border-b last:border-0 hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium">{{ $pointage->user->nom_complet }}</td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($pointage->date_jour)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-mono">{{ $pointage->heure_arrivee ? \Carbon\Carbon::parse($pointage->heure_arrivee)->format('H:i:s') : '-' }}</td>
                                <td class="px-6 py-4 font-mono">{{ $pointage->heure_depart ? \Carbon\Carbon::parse($pointage->heure_depart)->format('H:i:s') : '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full uppercase
                                        {{ $pointage->statut_presence === 'a_l_heure' ? 'bg-green-100 text-green-800' : 
                                          ($pointage->statut_presence === 'en_retard' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ str_replace('_', ' ', $pointage->statut_presence) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun pointage.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>
</x-app-layout>
