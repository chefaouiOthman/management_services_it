<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Structure Organisationnelle (Départements)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('success') }}</div>
            @endif

            @if(auth()->user()->hasRole('Admin'))

            {{-- ===== ADMIN : Full CRUD ===== --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-card class="md:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Ajouter un Département</h3>
                    <form action="{{ route('departements.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="nom_departement" value="Nom du Département *" />
                            <x-text-input name="nom_departement" required class="mt-1 block w-full" placeholder="Ex: Ressources Humaines, IT..." />
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 transition">Enregistrer</button>
                    </form>
                </x-card>

<x-search-filters :search="request('search')" searchPlaceholder="Rechercher par nom de département..." :filters="[]" />

                <x-card class="md:col-span-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Départements Existants</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">ID</th>
                                    <th scope="col" class="px-6 py-3">Nom du Département</th>
                                    <th scope="col" class="px-6 py-3">Membres</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departements as $dept)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 font-mono font-bold">{{ $dept->id }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $dept->nom_departement }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ ($dept->employes?->count() ?? 0) + ($dept->stagiaires?->count() ?? 0) }} membres</td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <a href="{{ route('departements.show', $dept->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Voir les membres</a>
                                            <a href="{{ route('departements.edit', $dept->id) }}" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">Modifier</a>
                                            <form action="{{ route('departements.destroy', $dept->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Confirmer la suppression de {{ $dept->nom_departement }} ?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucun département configuré.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            @else

            {{-- ===== EMPLOYÉ / STAGIAIRE : Read-only, single department ===== --}}
            <x-card>
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $departement->nom_departement }}</h3>
                        <x-badge type="info">{{ ($departement->employes?->count() ?? 0) + ($departement->stagiaires?->count() ?? 0) }} membre(s)</x-badge>
                    </div>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nom</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Rôle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departement->employes as $employe)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $employe->user?->nom_complet ?? 'Utilisateur introuvable' }}</td>
                                    <td class="px-6 py-4">{{ $employe->user?->email ?? 'N/A' }}</td>
                                    <td class="px-6 py-4"><x-badge type="info">Employé</x-badge></td>
                                </tr>
                            @endforeach
                            @forelse($departement->stagiaires as $stagiaire)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $stagiaire->user?->nom_complet ?? 'Utilisateur introuvable' }}</td>
                                    <td class="px-6 py-4">{{ $stagiaire->user?->email ?? 'N/A' }}</td>
                                    <td class="px-6 py-4"><x-badge type="warning">Stagiaire</x-badge></td>
                                </tr>
                            @endforeach
                            @if(($departement->employes?->isEmpty() ?? true) && ($departement->stagiaires?->isEmpty() ?? true))
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">Aucun membre dans ce département.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{ $departements->appends(request()->query())->links() }}
            </x-card>

            @endif

        </div>
    </div>
</x-app-layout>