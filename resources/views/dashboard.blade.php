<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tableau de Bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            @php
                $isAdminOrSuperAdmin = auth()->user()->hasAnyRole(['Admin', 'Super Admin']);
            @endphp

            @if(!$isAdminOrSuperAdmin)
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
                        <div class="text-center">
                            <p class="text-5xl font-bold font-mono text-gray-900 dark:text-white tracking-widest" x-text="time">--:--:--</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 capitalize" x-text="date"></p>
                        </div>

                        <div class="flex flex-col items-center gap-2">
                            @if(!$pointageJour)
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
                                    &mdash; <x-badge type="{{ $pointageJour->statut_presence == 'a_l_heure' ? 'success' : 'warning' }}">{{ str_replace('_', ' ', $pointageJour->statut_presence) }}</x-badge>
                                </p>

                            @else
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
            @endif

            {{-- FINANCE KPIs & CHARTS -- Admin / Super Admin only --}}
            @if($isAdminOrSuperAdmin)
            <div class="space-y-6">
                {{-- Modern KPI Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Total Entrées --}}
                    <div class="group relative bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-12 translate-x-12 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-white/80 text-sm font-semibold uppercase tracking-wider">Total Entrées</span>
                                <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                                </svg>
                            </div>
                            <p class="text-4xl font-black text-white font-mono tracking-tight">+ {{ number_format($kpis['total_entrees'], 2, ',', ' ') }} DHS</p>
                            <p class="text-white/60 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                                Encaissements validés
                            </p>
                        </div>
                    </div>

                    {{-- Total Sorties --}}
                    <div class="group relative bg-gradient-to-br from-rose-500 to-rose-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-12 translate-x-12 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-white/80 text-sm font-semibold uppercase tracking-wider">Total Sorties</span>
                                <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                </svg>
                            </div>
                            <p class="text-4xl font-black text-white font-mono tracking-tight">- {{ number_format($kpis['total_sorties'], 2, ',', ' ') }} DHS</p>
                            <p class="text-white/60 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" /></svg>
                                Paie & Frais décaissés
                            </p>
                        </div>
                    </div>

                    {{-- Solde Net --}}
                    <div class="group relative bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-12 translate-x-12 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-white/80 text-sm font-semibold uppercase tracking-wider">Solde de Trésorerie</span>
                                <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-4xl font-black text-white font-mono tracking-tight">{{ number_format($kpis['solde_net'], 2, ',', ' ') }} DHS</p>
                            <p class="text-white/60 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                @if($kpis['solde_net'] >= 0) Trésorerie positive @else Déficit @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Charts Section --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Chart 1: Area Chart - Evolution Mensuelle --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Évolution des Flux Financiers</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Entrées vs Sorties mensuelles ({{ date('Y') }})</p>
                        <div id="chart-evolution" class="w-full"></div>
                    </div>

                    {{-- Chart 2: Donut Chart - Répartition des Dépenses --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Répartition des Dépenses</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Masse Salariale vs Frais de Fonctionnement</p>
                        <div id="chart-depenses" class="w-full"></div>
                    </div>
                </div>

                {{-- Chart 3: Bar Chart - Performance Facturation (full width) --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Performance de Facturation</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Montant Facturé vs Encaissé par mois ({{ date('Y') }})</p>
                    <div id="chart-facturation" class="w-full"></div>
                </div>
            </div>
            @endif

            {{-- Pointages Récent (Admin/Super Admin) --}}
            @if($isAdminOrSuperAdmin && isset($tousLesPointagesRecents))
            <x-card>
                <x-slot name="header">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pointages Récents (Tous les employés)</h3>
                        @can('pointage-view')
                        <a href="{{ route('pointages.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Voir tout l'historique &rarr;</a>
                        @endcan
                    </div>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Employé</th>
                                <th scope="col" class="px-4 py-3">Date</th>
                                <th scope="col" class="px-4 py-3">Arrivée</th>
                                <th scope="col" class="px-4 py-3">Départ</th>
                                <th scope="col" class="px-4 py-3">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tousLesPointagesRecents as $p)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $p->user->nom_complet }}</td>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($p->date_jour)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">{{ $p->heure_arrivee ? \Carbon\Carbon::parse($p->heure_arrivee)->format('H:i') : '-' }}</td>
                                    <td class="px-4 py-3">{{ $p->heure_depart ? \Carbon\Carbon::parse($p->heure_depart)->format('H:i') : '-' }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $sc = match($p->statut_presence) {
                                                'a_l_heure'       => 'success',
                                                'en_retard'       => 'warning',
                                                'depart_anticipe' => 'danger',
                                                default           => 'gray',
                                            };
                                            $sl = match($p->statut_presence) {
                                                'a_l_heure'       => "À l'heure",
                                                'en_retard'       => 'En retard',
                                                'depart_anticipe' => 'Départ anticipé',
                                                default           => $p->statut_presence,
                                            };
                                        @endphp
                                        <x-badge :type="$sc">{{ $sl }}</x-badge>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Aucun pointage récent.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
            @endif

            {{-- Non-admin: own pointages --}}
            @if(!$isAdminOrSuperAdmin)
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
                                                'a_l_heure'       => "À l'heure",
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
            @endif

        </div>
    </div>

    @if($isAdminOrSuperAdmin)
    {{-- ApexCharts CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var evolutionData = @json($evolution);
            var depensesData = @json($depenses);
            var facturationData = @json($facturation);

            var months = evolutionData.map(function (d) { return d.month; });

            // 1. Area Chart - Evolution
            var optionsEvolution = {
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    animations: {
                        initialAnimation: { enabled: true, speed: 800 },
                        dynamicAnimation: { enabled: true, speed: 350 }
                    }
                },
                series: [
                    {
                        name: 'Entrées',
                        data: evolutionData.map(function (d) { return d.entrees; }),
                        color: '#10B981'
                    },
                    {
                        name: 'Sorties',
                        data: evolutionData.map(function (d) { return d.sorties; }),
                        color: '#F43F5E'
                    }
                ],
                xaxis: {
                    categories: months,
                    labels: { style: { colors: '#9CA3AF' } }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) { return val.toLocaleString('fr-FR'); },
                        style: { colors: '#9CA3AF' }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) { return val.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' DHS'; }
                    }
                },
                grid: { borderColor: '#F3F4F6' },
                legend: {
                    position: 'top',
                    labels: { colors: '#6B7280' }
                }
            };
            var chartEvolution = new ApexCharts(document.querySelector('#chart-evolution'), optionsEvolution);
            chartEvolution.render();

            // 2. Donut Chart - Répartition des Dépenses
            var optionsDepenses = {
                chart: {
                    type: 'donut',
                    height: 320,
                    animations: {
                        initialAnimation: { enabled: true, speed: 800 },
                        dynamicAnimation: { enabled: true, speed: 350 }
                    }
                },
                series: [depensesData.masse_salariale, depensesData.frais_fonctionnement],
                labels: ['Masse Salariale', 'Frais de Fonctionnement'],
                colors: ['#6366F1', '#F59E0B'],
                dataLabels: {
                    enabled: true,
                    formatter: function (val) { return val.toFixed(1) + '%'; },
                    style: { fontSize: '12px', fontWeight: 'bold' }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function () {
                                        var total = depensesData.masse_salariale + depensesData.frais_fonctionnement;
                                        return total.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' DHS';
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) { return val.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' DHS'; }
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: { colors: '#6B7280' }
                },
                responsive: [{ breakpoint: 480, options: { chart: { height: 280 }, legend: { position: 'bottom' } } }]
            };
            var chartDepenses = new ApexCharts(document.querySelector('#chart-depenses'), optionsDepenses);
            chartDepenses.render();

            // 3. Bar Chart - Performance Facturation
            var optionsFacturation = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false },
                    animations: {
                        initialAnimation: { enabled: true, speed: 800 },
                        dynamicAnimation: { enabled: true, speed: 350 }
                    }
                },
                series: [
                    {
                        name: 'Facturé',
                        data: facturationData.map(function (d) { return d.facture; }),
                        color: '#3B82F6'
                    },
                    {
                        name: 'Encaissé',
                        data: facturationData.map(function (d) { return d.encaisse; }),
                        color: '#10B981'
                    }
                ],
                xaxis: {
                    categories: facturationData.map(function (d) { return d.month; }),
                    labels: { style: { colors: '#9CA3AF' } }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) { return val.toLocaleString('fr-FR'); },
                        style: { colors: '#9CA3AF' }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                tooltip: {
                    y: {
                        formatter: function (val) { return val.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' DHS'; }
                    }
                },
                grid: { borderColor: '#F3F4F6' },
                legend: {
                    position: 'top',
                    labels: { colors: '#6B7280' }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '60%',
                        dataLabels: { position: 'top' }
                    }
                }
            };
            var chartFacturation = new ApexCharts(document.querySelector('#chart-facturation'), optionsFacturation);
            chartFacturation.render();
        });
    </script>
    @endif
</x-app-layout>
