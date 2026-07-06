<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Programme : {{ $catalogue->titre_formation }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Prix Standard : {{ number_format($catalogue->prix_standard, 2, ',', ' ') }} DHS</p>
            </div>
            <div class="flex gap-2">
                @can('catalogue-formation-edit')
                <a href="{{ route('catalogue.edit', $catalogue->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Modifier Programme
                </a>
                @endcan
                <a href="{{ route('catalogue.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'programme' }" @hashchange.window="tab = window.location.hash.substring(1)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Navigation Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#programme" :class="tab === 'programme' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Détails du Programme</a>
                    <a href="#supports" :class="tab === 'supports' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Supports de Cours ({{ $catalogue->supportCours->count() }})</a>
                    <a href="#sessions" :class="tab === 'sessions' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">Sessions Planifiées ({{ $catalogue->sessions->count() }})</a>
                </nav>
            </div>

            <!-- TAB: PROGRAMME -->
            <div x-show="tab === 'programme'" x-cloak class="space-y-4">
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Description</h3>
                    </x-slot>
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($catalogue->description_programme)) !!}
                    </div>
                </x-card>
            </div>

            <!-- TAB: SUPPORTS -->
            <div x-show="tab === 'supports'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Bibliothèque Pédagogique</h3>
                    @can('support-cours-create')
                    <a href="{{ route('supports.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                        + Ajouter un Support
                    </a>
                    @endcan
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($catalogue->supportCours as $support)
                        <x-card>
                            <div class="flex flex-col h-full justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" title="{{ $support->nom_fichier }}">
                                            {{ $support->nom_fichier }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">Ajouté le {{ $support->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                    <a href="{{ route('supports.download', $support->id) }}" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Télécharger
                                    </a>
                                </div>
                            </div>
                        </x-card>
                    @empty
                        <div class="col-span-full py-8 text-center text-gray-500 bg-white dark:bg-gray-800 rounded-lg">Aucun document attaché à ce programme.</div>
                    @endforelse
                </div>
            </div>

            <!-- TAB: SESSIONS -->
            <div x-show="tab === 'sessions'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Sessions Planifiées</h3>
                    @can('session-formation-create')
                    <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                        + Planifier une Session
                    </a>
                    @endcan
                </div>

                <x-card>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Dates</th>
                                    <th scope="col" class="px-6 py-3">Salle Physique</th>
                                    <th scope="col" class="px-6 py-3">Salle Virtuelle</th>
                                    <th scope="col" class="px-6 py-3">Statut (Temporel)</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($catalogue->sessions as $session)
                                    @php
                                        $now = now()->startOfDay();
                                        $debut = \Carbon\Carbon::parse($session->date_debut);
                                        $fin = \Carbon\Carbon::parse($session->date_fin);
                                        
                                        if ($now->lt($debut)) {
                                            $statutTxt = 'À venir';
                                            $statutCol = 'info';
                                        } elseif ($now->between($debut, $fin)) {
                                            $statutTxt = 'En cours';
                                            $statutCol = 'success';
                                        } else {
                                            $statutTxt = 'Terminée';
                                            $statutCol = 'gray';
                                        }
                                    @endphp
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            Du {{ $debut->format('d/m/Y') }}<br>Au {{ $fin->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4">{{ $session->salle_concrete ?? '-' }}</td>
                                        <td class="px-6 py-4">
                                            @if($session->salle_virtuelle)
                                                <a href="{{ $session->salle_virtuelle }}" target="_blank" class="text-indigo-600 hover:underline">Lien Visio</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <x-badge :type="$statutCol">{{ $statutTxt }}</x-badge>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('sessions.show', $session->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Voir le Hub</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucune session planifiée.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
