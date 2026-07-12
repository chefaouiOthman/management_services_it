<!-- resources/views/layouts/sidebar.blade.php -->
<aside class="flex flex-col w-64 h-full px-4 py-8 overflow-y-auto bg-white border-r border-gray-200 shadow-sm transition-transform duration-300 ease-in-out z-40 fixed md:relative transform"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full md:translate-x-0': !sidebarOpen}">
    
    <div class="flex items-center justify-between mb-8">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="w-auto h-8 text-indigo-600 fill-current" />
            <span class="text-xl font-bold tracking-tight text-gray-900">ERP <span class="text-indigo-600">Pro</span></span>
        </a>
        <button @click="sidebarOpen = false" class="md:hidden p-2 text-gray-500 rounded-lg hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    
    <div class="flex flex-col justify-between flex-1 mt-2">
        <nav class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-x-3 px-3 py-2.5 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-indigo-700' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard</span>
            </a>

            <!-- Module 1: Humain -->
            @can('user-view')
            <div x-data="{ expanded: {{ request()->routeIs('users.*') || request()->routeIs('employes.*') || request()->routeIs('stagiaires.*') || request()->routeIs('clients.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors rounded-xl hover:bg-gray-100">
                    <div class="flex items-center gap-x-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Humains & Profils</span>
                    </div>
                    <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="expanded" x-transition class="pl-11 pr-3 mt-1 space-y-1">
                    <a href="{{ route('users.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('users.index') && request()->get('role') == null ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Utilisateurs</a>
                    <a href="{{ route('users.index', ['role' => 'employe']) }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('users.index') && request()->get('role') == 'employe' ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Employés</a>
                    <a href="{{ route('users.index', ['role' => 'stagiaire']) }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('users.index') && request()->get('role') == 'stagiaire' ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Stagiaires</a>
                    <a href="{{ route('users.index', ['role' => 'client']) }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('users.index') && request()->get('role') == 'client' ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Clients</a>
                </div>
            </div>
            @endcan

            <!-- Module 2: RH & Accès -->
            <div x-data="{ expanded: {{ request()->routeIs('departements.*') || request()->routeIs('zones.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors rounded-xl hover:bg-gray-100">
                    <div class="flex items-center gap-x-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>RH & Accès</span>
                    </div>
                    <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="expanded" x-transition class="pl-11 pr-3 mt-1 space-y-1">
                    @can('user-view')
                    <a href="{{ route('departements.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('departements.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Départements</a>
                    @endcan
                    @can('zone-view')
                    <a href="{{ route('zones.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('zones.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Zones d'accès et historiques de passages</a>
                    @endcan
                </div>
            </div>

            <!-- Module 3: Projets & Production -->
            @can('projet-view')
            <div x-data="{ expanded: {{ request()->routeIs('projets.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors rounded-xl hover:bg-gray-100">
                    <div class="flex items-center gap-x-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span>Projets & Production</span>
                    </div>
                    <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="expanded" x-transition class="pl-11 pr-3 mt-1 space-y-1">
                    <a href="{{ route('projets.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('projets.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Projets</a>
                    <a href="{{ route('taches.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('taches.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Tâches</a>
                    <a href="{{ route('feuille_temps.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('feuille_temps.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Feuilles de Temps</a>
                    <a href="{{ route('technologies.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('technologies.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Technologies</a>
                </div>
            </div>
            @endcan

            <!-- Module 4: Académie -->
            @can('session-formation-view')
            <div x-data="{ expanded: {{ request()->routeIs('sessions.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors rounded-xl hover:bg-gray-100">
                    <div class="flex items-center gap-x-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                        <span>Académie</span>
                    </div>
                    <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="expanded" x-transition class="pl-11 pr-3 mt-1 space-y-1">
                    <a href="{{ route('catalogue.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('catalogue.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Catalogue Formations</a>
                    <a href="{{ route('sessions.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('sessions.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Sessions</a>
                    <a href="{{ route('inscriptions.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('inscriptions.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Inscriptions</a>
                    <a href="{{ route('supports.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('supports.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Supports de Cours</a>
                </div>
            </div>
            @endcan

            <!-- Module 5: Actifs IT -->
            @can('asset-view')
            <div x-data="{ expanded: {{ request()->routeIs('assets.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors rounded-xl hover:bg-gray-100">
                    <div class="flex items-center gap-x-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>Inventaire IT</span>
                    </div>
                    <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="expanded" x-transition class="pl-11 pr-3 mt-1 space-y-1">
                    <a href="{{ route('assets.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('assets.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Matériels IT</a>
                    <a href="{{ route('type_materiels.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('type_materiels.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Types de Matériel</a>
                    <a href="{{ route('licences.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('licences.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Licences Logiciel</a>
                </div>
            </div>
            @endcan

            <!-- Module 6: Trésorerie -->
            @can('flux-tresorerie-view')
            <div x-data="{ expanded: {{ request()->routeIs('flux_tresoreries.*') || request()->routeIs('note_de_frais.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors rounded-xl hover:bg-gray-100">
                    <div class="flex items-center gap-x-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Finance & Compta</span>
                    </div>
                    <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="expanded" x-transition class="pl-11 pr-3 mt-1 space-y-1">
                    <a href="{{ route('flux_tresoreries.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('flux_tresoreries.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Flux de Trésorerie</a>
                    <a href="#" class="block px-3 py-2 text-sm rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-50">Catégories de Flux</a>
                    <a href="#" class="block px-3 py-2 text-sm rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-50">Factures</a>
                    <a href="#" class="block px-3 py-2 text-sm rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-50">Fiches de Paie</a>
                    <a href="{{ route('note_de_frais.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('note_de_frais.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Notes de Frais</a>
                </div>
            </div>
            @endcan

        </nav>
    </div>
</aside>
