<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($asset) ? 'Modifier Matériel : ' . $asset->marque : 'Ajouter un Matériel' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ isset($asset) ? route('assets.update', $asset->id) : route('assets.store') }}" method="POST" class="space-y-6">
                    @csrf
                    @if(isset($asset))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="type_materiel_id" value="Type de Matériel *" />
                            <select name="type_materiel_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500">
                                @foreach(\App\Models\TypeMateriel::all() as $type)
                                    <option value="{{ $type->id }}" @selected(old('type_materiel_id', $asset->type_materiel_id ?? '') == $type->id)>{{ $type->libelle_type }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('type_materiel_id')" />
                        </div>
                        <div>
                            <x-input-label for="num_serie" value="Numéro de Série *" />
                            <x-text-input id="num_serie" name="num_serie" type="text" class="mt-1 block w-full" :value="old('num_serie', $asset->num_serie ?? '')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('num_serie')" />
                        </div>
                        <div>
                            <x-input-label for="marque" value="Marque *" />
                            <x-text-input id="marque" name="marque" type="text" class="mt-1 block w-full" :value="old('marque', $asset->marque ?? '')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('marque')" />
                        </div>
                        <div>
                            <x-input-label for="modele" value="Modèle *" />
                            <x-text-input id="modele" name="modele" type="text" class="mt-1 block w-full" :value="old('modele', $asset->modele ?? '')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('modele')" />
                        </div>
                        <div>
                            <x-input-label for="date_achat_actif" value="Date d'Achat" />
                            <x-text-input id="date_achat_actif" name="date_achat_actif" type="date" class="mt-1 block w-full" :value="old('date_achat_actif', isset($asset) && $asset->date_achat_actif ? $asset->date_achat_actif->format('Y-m-d') : '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('date_achat_actif')" />
                        </div>
                        <div>
                            <x-input-label for="prix_achat" value="Valeur d'Achat (DHS)" />
                            <x-text-input id="prix_achat" name="prix_achat" type="number" step="0.01" class="mt-1 block w-full" :value="old('prix_achat', $asset->prix_achat ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('prix_achat')" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="statut_materiel" value="Statut Actuel *" />
                            <select name="statut_materiel" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500">
                                <option value="disponible" @selected(old('statut_materiel', $asset->statut_materiel ?? '') === 'disponible')>Disponible</option>
                                <option value="attribue" @selected(old('statut_materiel', $asset->statut_materiel ?? '') === 'attribue')>Attribué</option>
                                <option value="en_panne" @selected(old('statut_materiel', $asset->statut_materiel ?? '') === 'en_panne')>En Panne</option>
                                <option value="reforme" @selected(old('statut_materiel', $asset->statut_materiel ?? '') === 'reforme')>Réformé</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('statut_materiel')" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 transition">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 transition">Enregistrer</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
