<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Hub Sécurité : {{ $zone->nom_salle }}
                    <span class="ml-2 font-mono text-sm text-gray-500">[{{ $zone->code_zone }}]</span>
                </h2>
            </div>
            <div class="flex gap-2">
                @can('zone-edit')
                <a href="{{ route('zones.edit', $zone->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Modifier
                </a>
                @endcan
                <a href="{{ route('zones.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- =============================== -->
            <!--  FICHE TECHNIQUE DE LA ZONE     -->
            <!-- =============================== -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Caractéristiques de la Zone</h3>
                </x-slot>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Code Zone</p>
                        <p class="font-mono font-bold text-gray-900 dark:text-white text-lg">{{ $zone->code_zone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Niveau Requis</p>
                        <p class="font-bold text-gray-900 dark:text-white text-lg">Niv. {{ $zone->niveau_requis }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Statut</p>
                        <div class="mt-1">
                            @if($zone->est_active)
                                <x-badge type="success">Active</x-badge>
                            @else
                                <x-badge type="danger">Désactivée</x-badge>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Créée le</p>
                        <p class="text-gray-900 dark:text-white">{{ $zone->created_at?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-card>

            <!-- =============================== -->
            <!--  SECTION 1 : PRÉSENCES ACTIVES  -->
            <!-- =============================== -->
            <x-card>
                <x-slot name="header">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-3 w-3 rounded-full bg-green-400 animate-ping"></span>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Présences Globales Aujourd'hui</h3>
                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">(Personnel actuellement dans l'entreprise)</span>
                    </div>
                </x-slot>

                @if($presencesActives->isEmpty())
                    <p class="text-sm text-gray-500 py-2">Aucune présence active enregistrée pour le moment.</p>
                @else
                    <div class="flex flex-wrap gap-3">
                        @foreach($presencesActives as $presence)
                            <div class="flex items-center gap-2 px-3 py-2 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-700">
                                <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $presence->user?->nom_complet ?? 'Inconnu' }}</p>
                                    <p class="text-xs text-gray-500">Depuis {{ \Carbon\Carbon::parse($presence->heure_arrivee)->format('H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <!-- =============================== -->
            <!--  SECTION 2 : JOURNAL DES ACCÈS  -->
            <!-- =============================== -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Journal Historique des Accès</h3>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Utilisateur</th>
                                <th scope="col" class="px-6 py-3">Horodatage</th>
                                <th scope="col" class="px-6 py-3">Résultat de la Tentative</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historiquesPagines as $passage)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $passage->user?->nom_complet ?? 'Utilisateur inconnu' }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">
                                        {{ \Carbon\Carbon::parse($passage->horodatage)->format('d/m/Y à H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $badgeType = match($passage->tentative_statut) {
                                                'autorise'                    => 'success',
                                                'refuse_niveau_insuffisant'   => 'danger',
                                                'refuse_zone_desactivee'      => 'gray',
                                                default                       => 'info',
                                            };
                                            $badgeLabel = match($passage->tentative_statut) {
                                                'autorise'                    => '✓ Autorisé',
                                                'refuse_niveau_insuffisant'   => '✗ Refusé (niveau insuffisant)',
                                                'refuse_zone_desactivee'      => '✗ Refusé (zone désactivée)',
                                                default                       => $passage->tentative_statut,
                                            };
                                        @endphp
                                        <x-badge :type="$badgeType">{{ $badgeLabel }}</x-badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                        Aucun passage enregistré pour cette zone.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($historiquesPagines->hasPages())
                    <div class="mt-4 px-2">
                        {{ $historiquesPagines->links() }}
                    </div>
                @endif
            </x-card>

        </div>
    </div>
</x-app-layout>
