<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Catégories de Flux
            </h2>
            @can('categorie-flux-create')
            <div x-data="{ openModal: false }">
                <button @click="openModal = true" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    + Nouvelle Catégorie
                </button>
                <div x-show="openModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="openModal = false">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-md mx-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Nouvelle Catégorie de Flux</h3>
                        <form action="{{ route('categorie_flux.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="libelle_categorie" value="Libellé Catégorie *" />
                                <x-text-input name="libelle_categorie" required class="mt-1 block w-full" :value="old('libelle_categorie')" placeholder="Ex: Vente / Facture Client" />
                                <x-input-error class="mt-2" :messages="$errors->get('libelle_categorie')" />
                            </div>
                            <div>
                                <x-input-label for="code_comptable" value="Code Comptable" />
                                <x-text-input name="code_comptable" class="mt-1 block w-full" :value="old('code_comptable')" placeholder="Ex: 701" />
                                <x-input-error class="mt-2" :messages="$errors->get('code_comptable')" />
                            </div>
                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="openModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">Annuler</button>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold hover:bg-indigo-700">Créer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-6 py-3">Libellé</th>
                                <th class="px-6 py-3">Code Comptable</th>
                                <th class="px-6 py-3 text-center">Nb. Flux Associés</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $categorie)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $categorie->libelle_categorie }}</td>
                                    <td class="px-6 py-4 font-mono text-sm">{{ $categorie->code_comptable ?? '—' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-full font-semibold">{{ $categorie->fluxTresoreries()->count() }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        @can('categorie-flux-edit')
                                        <a href="{{ route('categorie_flux.edit', $categorie->id) }}" class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold rounded transition shadow-sm">Modifier</a>
                                        @endcan
                                        @can('categorie-flux-delete')
                                        <form action="{{ route('categorie_flux.destroy', $categorie->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer définitivement cette catégorie ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 text-xs font-bold rounded transition shadow-sm">Supprimer</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucune catégorie de flux créée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
