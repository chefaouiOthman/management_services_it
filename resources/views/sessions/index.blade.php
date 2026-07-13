<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Académie — Formations & Sessions
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'sessions' }" @hashchange.window="tab = window.location.hash.substring(1)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">{{ session('success') }}</div>
            @endif

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 rounded-t-lg shadow-sm">
                <nav class="-mb-px flex space-x-8">
                    <a href="#sessions" :class="tab === 'sessions' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">
                        Sessions Planifiées
                    </a>
                    <a href="#formations" :class="tab === 'formations' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">
                        Formations Disponibles (Catalogue)
                    </a>
                </nav>
            </div>

            <!-- TAB 1 : SESSIONS -->
            <div x-show="tab === 'sessions'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Toutes les sessions</h3>
                    <div class="flex gap-2">
                        @can('session-formation-create')
                        <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                            + Planifier Session
                        </a>
                        @endcan
                    </div>
                </div>

                <x-card>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Programme</th>
                                    <th scope="col" class="px-6 py-3">Dates</th>
                                    <th scope="col" class="px-6 py-3">Formateurs</th>
                                    <th scope="col" class="px-6 py-3">Lieux</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('sessions.show', $session->id) }}" class="text-indigo-600 hover:underline">
                                                {{ $session->catalogueFormation?->titre_formation ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4">
                                            Du {{ \Carbon\Carbon::parse($session->date_debut)->format('d/m/Y') }}<br>
                                            Au {{ \Carbon\Carbon::parse($session->date_fin)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($session->formateurs as $formateur)
                                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded border border-gray-200 dark:border-gray-600">
                                                        {{ $formateur->user?->nom_complet ?? 'Inconnu' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($session->salle_concrete)
                                                <span class="block">📍 {{ $session->salle_concrete }}</span>
                                            @endif
                                            @if($session->salle_virtuelle)
                                                <a href="{{ $session->salle_virtuelle }}" target="_blank" class="text-indigo-600 hover:underline text-xs">🔗 Visio</a>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2 flex-wrap">
                                                <a href="{{ route('sessions.show', $session->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm">Hub</a>
                                                
                                                <a href="{{ route('evaluations.index', ['session' => $session->id]) }}" class="text-purple-600 hover:text-purple-900 font-medium text-sm">Évaluations</a>

                                                @can('session-formation-edit')
                                                <a href="{{ route('sessions.edit', $session->id) }}" class="text-gray-500 hover:text-gray-900 font-medium text-sm">Éditer</a>
                                                @endcan

                                                @can('session-formation-delete')
                                                <form action="{{ route('sessions.destroy', $session->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette session ? Les inscriptions liées seront aussi supprimées.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm">Supprimer</button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            Aucune session de formation planifiée.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            <!-- TAB 2 : FORMATIONS (CATALOGUE) -->
            <div x-show="tab === 'formations'" x-cloak class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Formations Disponibles</h3>
                    @can('catalogue-formation-create')
                    <a href="{{ route('catalogue.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        + Nouveau Programme
                    </a>
                    @endcan
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($catalogues as $catalogue)
                        <x-card class="flex flex-col h-full">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white flex-1 pr-2">{{ $catalogue->titre_formation }}</h4>
                                <span class="text-base font-mono text-indigo-600 dark:text-indigo-400 font-bold whitespace-nowrap">{{ number_format($catalogue->prix_standard, 0, ',', ' ') }} DHS</span>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3 flex-1">
                                {{ $catalogue->description_programme }}
                            </p>

                            <!-- Supports de Cours -->
                            @if($catalogue->supportCours?->isNotEmpty() ?? false)
                                <div class="mb-4 border border-gray-100 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-900/50">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Supports de cours</p>
                                    <ul class="space-y-1">
                                        @foreach($catalogue->supportCours as $support)
                                            <li class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                <a href="{{ $support->url_stockage }}" target="_blank" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline truncate">
                                                    {{ $support->nom_fichier }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="mb-4 text-xs text-gray-400 italic">Aucun support attaché.</div>
                            @endif

                            <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-1 text-sm text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    {{ $catalogue->supportCours?->count() ?? 0 }} Support(s)
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('catalogue.show', $catalogue->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium text-sm">Ouvrir</a>
                                    @can('catalogue-formation-edit')
                                    <a href="{{ route('catalogue.edit', $catalogue->id) }}" class="text-gray-500 hover:text-gray-900 font-medium text-sm">Éditer</a>
                                    @endcan
                                    @can('catalogue-formation-delete')
                                    <form action="{{ route('catalogue.destroy', $catalogue->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer ce programme de formation ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm">Supprimer</button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </x-card>
                    @empty
                        <div class="col-span-full py-8 text-center text-gray-500">
                            Aucun programme de formation dans le catalogue.
                            @can('catalogue-formation-create')
                            <br><a href="{{ route('catalogue.create') }}" class="text-indigo-600 hover:underline mt-2 inline-block">→ Créer la première formation</a>
                            @endcan
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
