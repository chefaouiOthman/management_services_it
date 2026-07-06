<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tableau de Bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- =============================== -->
            <!--  WIDGET DE POINTAGE             -->
            <!-- =============================== -->
            <div x-data="{
                time: '',
                date: '',
                loading: false,
                init() {
                    this.tick();
                    setInterval(() => this.tick(), 1000);
                },
                tick() {
                    const now = new Date();
                    this.time = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    this.date = now.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                }
            }" x-init="init()">
                <x-card>
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <!-- Horloge -->
                        <div class="text-center">
                            <p class="text-5xl font-bold font-mono text-gray-900 dark:text-white tracking-widest" x-text="time">--:--:--</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 capitalize" x-text="date"></p>
                        </div>

                        <!-- Bouton de Badge -->
                        <div class="flex flex-col items-center gap-2">
                            @if(!$pointageJour)
                                {{-- Pas encore badgé : Bouton Entrée --}}
                                <form method="POST" action="{{ route('pointages.badge') }}" @submit="loading = true">
                                    @csrf
                                    <button type="submit"
                                        :disabled="loading"
                                        class="inline-flex items-center gap-3 px-8 py-4 bg-green-600 hover:bg-green-700 active:bg-green-800 disabled:opacity-50 disabled:cursor-wait text-white font-bold text-lg rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300">
                                        <span x-show="!loading">
                                            <svg class="h-7 w-7 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            Pointer l'Entrée
                                        </span>
                                        <span x-show="loading" class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            Enregistrement...
                                        </span>
                                    </button>
                                </form>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Aucun pointage enregistré aujourd'hui.</p>

                            @elseif($pointageJour && !$pointageJour->heure_depart)
                                {{-- Arrivée enregistrée, pas encore de départ : Bouton Sortie --}}
                                <form method="POST" action="{{ route('pointages.badge') }}" @submit="loading = true">
                                    @csrf
                                    <button type="submit"
                                        :disabled="loading"
                                        class="inline-flex items-center gap-3 px-8 py-4 bg-red-600 hover:bg-red-700 active:bg-red-800 disabled:opacity-50 disabled:cursor-wait text-white font-bold text-lg rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-red-300">
                                        <span x-show="!loading">
                                            <svg class="h-7 w-7 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Pointer la Sortie
                                        </span>
                                        <span x-show="loading" class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            Enregistrement...
                                        </span>
                                    </button>
                                </form>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Arrivée enregistrée à : <strong>{{ \Carbon\Carbon::parse($pointageJour->heure_arrivee)->format('H:i') }}</strong>
                                    — <x-badge type="{{ $pointageJour->statut_presence == 'a_l_heure' ? 'success' : 'warning' }}">{{ str_replace('_', ' ', $pointageJour->statut_presence) }}</x-badge>
                                </p>

                            @else
                                {{-- Journée complète --}}
                                <div class="text-center p-4">
                                    <svg class="h-12 w-12 text-green-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="font-semibold text-gray-700 dark:text-gray-300">Journée complète enregistrée</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($pointageJour->heure_arrivee)->format('H:i') }}
                                        &rarr;
                                        {{ \Carbon\Carbon::parse($pointageJour->heure_depart)->format('H:i') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Infos Utilisateur -->
                        <div class="text-center md:text-right">
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ Auth::user()->nom_complet }}</p>
                            <div class="flex justify-center md:justify-end gap-1 mt-1 flex-wrap">
                                @foreach(Auth::user()->roles as $role)
                                    <x-badge type="info">{{ $role->name }}</x-badge>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- =============================== -->
            <!--  5 DERNIERS POINTAGES           -->
            <!-- =============================== -->
            <x-card>
                <x-slot name="header">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mes 5 derniers pointages</h3>
                        @can('pointage-view')
                        <a href="{{ route('pointages.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Voir tout l'historique &rarr;</a>
                        @endcan
                    </div>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Date</th>
                                <th scope="col" class="px-4 py-3">Arrivée</th>
                                <th scope="col" class="px-4 py-3">Départ</th>
                                <th scope="col" class="px-4 py-3">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($derniersPointages as $p)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($p->date_jour)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $p->heure_arrivee ? \Carbon\Carbon::parse($p->heure_arrivee)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $p->heure_depart ? \Carbon\Carbon::parse($p->heure_depart)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statutColor = match($p->statut_presence) {
                                                'a_l_heure'       => 'success',
                                                'en_retard'       => 'warning',
                                                'depart_anticipe' => 'danger',
                                                default           => 'gray',
                                            };
                                            $statutLabel = match($p->statut_presence) {
                                                'a_l_heure'       => 'À l\'heure',
                                                'en_retard'       => 'En retard',
                                                'depart_anticipe' => 'Départ anticipé',
                                                default           => $p->statut_presence,
                                            };
                                        @endphp
                                        <x-badge :type="$statutColor">{{ $statutLabel }}</x-badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">Aucun historique de pointage.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

        </div>
    </div>
</x-app-layout>
