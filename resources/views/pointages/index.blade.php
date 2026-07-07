<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Historique des Pointages
            </h2>
            @can('pointage-create')
            <a href="{{ route('pointages.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md font-bold text-xs hover:bg-indigo-700 transition">
                + Saisir Pointage Manuel
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('success') }}</div>
            @endif

            {{-- Filtre Admin --}}
            @if(Auth::user()->hasRole('Admin'))
            <x-card>
                <form method="GET" action="{{ route('pointages.index') }}" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filtrer par Employé</label>
                        <select name="user_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">Tous les employés</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->nom_complet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-700 transition">Filtrer</button>
                    @if(request('user_id'))
                        <a href="{{ route('pointages.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-300 transition">Réinitialiser</a>
                    @endif
                </form>
            </x-card>
            @endif

            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Employé</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Arrivée</th>
                                <th class="px-6 py-3">Départ</th>
                                <th class="px-6 py-3">Durée</th>
                                <th class="px-6 py-3">Statut</th>
                                @can('pointage-edit')
                                <th class="px-6 py-3 text-right">Actions</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pointages as $pointage)
                                @php
                                    $duree = null;
                                    if ($pointage->heure_arrivee && $pointage->heure_depart) {
                                        $diff = \Carbon\Carbon::parse($pointage->heure_arrivee)->diff(\Carbon\Carbon::parse($pointage->heure_depart));
                                        $duree = $diff->h . 'h ' . $diff->i . 'min';
                                    }
                                @endphp
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $pointage->user->nom_complet }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($pointage->date_jour)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 font-mono text-green-700">{{ $pointage->heure_arrivee ? \Carbon\Carbon::parse($pointage->heure_arrivee)->format('H:i:s') : '-' }}</td>
                                    <td class="px-6 py-4 font-mono text-red-700">{{ $pointage->heure_depart ? \Carbon\Carbon::parse($pointage->heure_depart)->format('H:i:s') : '-' }}</td>
                                    <td class="px-6 py-4 font-mono text-gray-600">{{ $duree ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full uppercase
                                            {{ $pointage->statut_presence === 'a_l_heure' ? 'bg-green-100 text-green-800' : 
                                              ($pointage->statut_presence === 'en_retard' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ str_replace('_', ' ', $pointage->statut_presence) }}
                                        </span>
                                    </td>
                                    @can('pointage-edit')
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('pointages.edit', $pointage->id) }}" class="text-indigo-600 hover:underline text-xs font-semibold">Modifier</a>
                                        <form action="{{ route('pointages.destroy', $pointage->id) }}" method="POST" class="inline ml-3" onsubmit="return confirm('Supprimer ce pointage ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold">Supprimer</button>
                                        </form>
                                    </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">Aucun pointage trouvé.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 px-4 pb-4">
                    {{ $pointages->appends(request()->query())->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
