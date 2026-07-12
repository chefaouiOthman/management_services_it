<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Membres du Département : ') }} {{ $departement->nom_departement }}
            </h2>
            <a href="{{ route('departements.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                Retour aux départements
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-card>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Employés ({{ $departement->employes->count() }})</h3>
                    <ul class="space-y-3">
                        @forelse($departement->employes as $employe)
                            <li class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 p-3 rounded shadow-sm border border-gray-100 dark:border-gray-600">
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $employe->user?->nom_complet ?? 'Utilisateur introuvable' }}</span>
                                    <span class="text-xs text-gray-500 block">{{ $employe->user?->email ?? 'N/A' }}</span>
                                </div>
                                <x-badge type="info">Employé</x-badge>
                            </li>
                        @empty
                            <p class="text-sm text-gray-500 italic">Aucun employé dans ce département.</p>
                        @endforelse
                    </ul>
                </x-card>

                <x-card>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Stagiaires ({{ $departement->stagiaires->count() }})</h3>
                    <ul class="space-y-3">
                        @forelse($departement->stagiaires as $stagiaire)
                            <li class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 p-3 rounded shadow-sm border border-gray-100 dark:border-gray-600">
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $stagiaire->user?->nom_complet ?? 'Utilisateur introuvable' }}</span>
                                    <span class="text-xs text-gray-500 block">{{ $stagiaire->user?->email ?? 'N/A' }}</span>
                                </div>
                                <x-badge type="warning">Stagiaire</x-badge>
                            </li>
                        @empty
                            <p class="text-sm text-gray-500 italic">Aucun stagiaire dans ce département.</p>
                        @endforelse
                    </ul>
                </x-card>
            </div>
            
        </div>
    </div>
</x-app-layout>