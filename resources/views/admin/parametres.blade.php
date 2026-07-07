<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ⚙️ Paramètres & Configuration Admin
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'technologies' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 rounded-t-lg shadow-sm">
                <nav class="-mb-px flex space-x-8">
                    <button @click="tab='technologies'" :class="tab==='technologies' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">Technologies</button>
                    <button @click="tab='type_materiels'" :class="tab==='type_materiels' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">Types de Matériels</button>
                    <button @click="tab='categories'" :class="tab==='categories' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition">Catégories Flux</button>
                </nav>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('success') }}</div>
            @endif

            <!-- TAB: TECHNOLOGIES -->
            <div x-show="tab === 'technologies'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Formulaire d'ajout -->
                    <x-card>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">➕ Ajouter une Technologie</h3>
                        <form action="{{ route('technologies.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="nom_tech" value="Nom *" />
                                <x-text-input name="nom_tech" required class="mt-1 block w-full" placeholder="Ex: Laravel, Vue.js, Docker..." />
                            </div>
                            <div>
                                <x-input-label for="version" value="Version *" />
                                <x-text-input name="version" required class="mt-1 block w-full" placeholder="Ex: 10.x, 3.4, 24.0..." />
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 transition">Enregistrer</button>
                        </form>
                    </x-card>
                    <!-- Liste existante -->
                    <x-card>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">📋 Technologies Existantes ({{ $technologies->count() }})</h3>
                        <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                            @forelse($technologies as $tech)
                                <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded border">
                                    <div>
                                        <span class="font-bold text-gray-900">{{ $tech->nom_tech }}</span>
                                        <span class="text-xs text-gray-500 ml-2 font-mono">v{{ $tech->version }}</span>
                                    </div>
                                    <form action="{{ route('technologies.destroy', $tech->id) }}" method="POST" onsubmit="return confirm('Supprimer {{ $tech->nom_tech }} ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold px-2 py-1">✕</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-gray-500 italic text-sm">Aucune technologie.</p>
                            @endforelse
                        </div>
                    </x-card>
                </div>
            </div>

            <!-- TAB: TYPE MATERIELS -->
            <div x-show="tab === 'type_materiels'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-card>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">➕ Ajouter un Type de Matériel</h3>
                        <form action="{{ route('type_materiels.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="libelle_type" value="Libellé *" />
                                <x-text-input name="libelle_type" required class="mt-1 block w-full" placeholder="Ex: Laptop, Serveur, Switch..." />
                            </div>
                            <div>
                                <x-input-label for="description_type" value="Description" />
                                <textarea name="description_type" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Optionnel..."></textarea>
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 transition">Enregistrer</button>
                        </form>
                    </x-card>
                    <x-card>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">📋 Types Existants ({{ $typeMaterialels->count() }})</h3>
                        <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                            @forelse($typeMaterialels as $type)
                                <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded border">
                                    <span class="font-bold text-gray-900">{{ $type->libelle_type }}</span>
                                    <form action="{{ route('type_materiels.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold px-2 py-1">✕</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-gray-500 italic text-sm">Aucun type.</p>
                            @endforelse
                        </div>
                    </x-card>
                </div>
            </div>

            <!-- TAB: CATEGORIES FLUX -->
            <div x-show="tab === 'categories'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-card>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">➕ Ajouter une Catégorie de Flux</h3>
                        <form action="{{ route('categorie_flux.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="libelle_categorie" value="Libellé *" />
                                <x-text-input name="libelle_categorie" required class="mt-1 block w-full" placeholder="Ex: Facturation Client, Charges salariales..." />
                            </div>
                            <div>
                                <x-input-label for="code_comptable" value="Code Comptable (optionnel)" />
                                <x-text-input name="code_comptable" class="mt-1 block w-full" placeholder="Ex: 7011, 6411..." />
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 transition">Enregistrer</button>
                        </form>
                    </x-card>
                    <x-card>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">📋 Catégories Existantes ({{ $categoriesFlux->count() }})</h3>
                        <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                            @forelse($categoriesFlux as $cat)
                                <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded border">
                                    <div>
                                        <span class="font-bold text-gray-900">{{ $cat->libelle_categorie }}</span>
                                        @if($cat->code_comptable)
                                            <span class="text-xs text-gray-500 ml-2 font-mono">{{ $cat->code_comptable }}</span>
                                        @endif
                                    </div>
                                    <form action="{{ route('categorie_flux.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold px-2 py-1">✕</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-gray-500 italic text-sm">Aucune catégorie.</p>
                            @endforelse
                        </div>
                    </x-card>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
