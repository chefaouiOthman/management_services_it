<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Inscriptions aux Formations
            </h2>
            @can('inscription-create')
            <a href="{{ route('inscriptions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Nouvelle Inscription
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

            <div class="space-y-4">
                @php
                    $groupedInscriptions = $inscriptions->groupBy('session_formation_id');
                @endphp

                @forelse($groupedInscriptions as $sessionId => $sessionInscriptions)
                    @php
                        $firstInscription = $sessionInscriptions->first();
                        $session = $firstInscription->sessionFormation;
                    @endphp
                    <details class="bg-white dark:bg-gray-800 rounded-lg shadow group">
                        <summary class="list-none cursor-pointer p-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <div class="flex items-center gap-4">
                                <span class="text-indigo-500 group-open:rotate-90 transition-transform duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </span>
                                <div>
                                    @if($session)
                                        <h3 class="font-semibold text-gray-900 dark:text-white">
                                            {{ $session->catalogueFormation?->titre_formation ?? 'Formation inconnue' }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Du {{ optional($session->date_debut)->format('d/m/Y') ?? 'N/A' }} 
                                            au {{ optional($session->date_fin)->format('d/m/Y') ?? 'N/A' }}
                                            &middot; <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $sessionInscriptions->count() }} inscrit(s)</span>
                                        </p>
                                    @else
                                        <h3 class="font-semibold text-red-600 dark:text-red-400">
                                            Session Orpheline (Supprimée)
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            <span class="font-medium text-red-600 dark:text-red-400">{{ $sessionInscriptions->count() }} inscrit(s)</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </summary>
                        
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-b-lg">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-white dark:bg-gray-800 dark:text-gray-400 rounded shadow-sm">
                                        <tr>
                                            <th class="px-4 py-3 rounded-l-lg">Utilisateur</th>
                                            <th class="px-4 py-3">Statut</th>
                                            <th class="px-4 py-3 text-right rounded-r-lg">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($sessionInscriptions as $inscription)
                                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                                    {{ $inscription->user?->nom_complet ?? 'Utilisateur Inconnu' }}
                                                    <br><span class="text-xs text-gray-500 dark:text-gray-400">{{ $inscription->user?->email ?? 'N/A' }}</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @switch($inscription->statut_inscription)
                                                        @case('valide')
                                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Validé</span>
                                                            @break
                                                        @case('annule')
                                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Annulé</span>
                                                            @break
                                                        @case('present')
                                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Présent</span>
                                                            @break
                                                        @case('certifie')
                                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">Certifié</span>
                                                            @break
                                                        @default
                                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Inconnu</span>
                                                    @endswitch
                                                </td>
                                                <td class="px-4 py-3 text-right space-x-3">
                                                    @can('inscription-edit')
                                                        <a href="{{ route('inscriptions.edit', $inscription->id) }}" class="text-indigo-600 dark:text-indigo-500 hover:underline font-medium text-xs">Modifier</a>
                                                    @endcan
                                                    @can('inscription-delete')
                                                        <form action="{{ route('inscriptions.destroy', $inscription->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette inscription ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 dark:text-red-500 hover:underline font-medium text-xs">Supprimer</button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </details>
                @empty
                    <div class="text-center text-gray-500 p-8 bg-white dark:bg-gray-800 rounded-lg shadow">
                        Aucune inscription trouvée.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
