@php
    $isEdit = isset($user) && $user->exists;
    $contratActuel = $isEdit && $user->employe ? $user->employe?->contratActuel : null;
    $defaultRole = 'Employe_Standard';
    if ($isEdit && $user->roles->isNotEmpty()) {
        $defaultRole = $user->roles->first()->name;
    } elseif ($isEdit) {
        if ($user->employe) $defaultRole = 'Employe_Standard';
        elseif ($user->stagiaire) $defaultRole = 'Stagiaire';
        elseif ($user->client) $defaultRole = 'Client';
        else $defaultRole = 'Admin';
    }
@endphp

<div x-data="{
    role: '{{ old('roles.0', old('roles', $defaultRole)) }}',
    isEdit: {{ $isEdit ? 'true' : 'false' }},
    typeContrat: '{{ old('type_contrat', $contratActuel->type_contrat ?? '') }}',
    get dateFinRequired() {
        return this.typeContrat === 'CDD' || this.typeContrat === 'Freelance';
    }
}">
    <form action="{{ $isEdit ? route('users.update', $user->id) : route('users.store') }}" method="POST" autocomplete="off" class="prevent-autofill">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <input style="display:none" type="email" name="fakeusernameremembered"/>
        <input style="display:none" type="password" name="fakepasswordremembered"/>

        <div class="space-y-6">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informations du profil</h3>
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full">
                        <x-input-label for="role" value="Type de profil" />
                        <select id="role" name="roles[]" x-model="role" :disabled="isEdit" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            @foreach($roles as $roleOption)
                                <option value="{{ $roleOption->name }}" {{ old('roles.0', old('roles', $defaultRole)) == $roleOption->name ? 'selected' : '' }}>
                                    {{ $roleOption->name === 'Admin' ? 'Employé Admin' : ($roleOption->name === 'Employe_Standard' ? 'Employé Standard' : $roleOption->name) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        <x-input-error :messages="$errors->get('roles.0')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="nom_complet" value="Nom Complet" />
                        <x-text-input id="nom_complet" name="nom_complet" type="text" class="mt-1 block w-full" value="{{ old('nom_complet', $user->nom_complet ?? '') }}" required autocomplete="off" />
                        <x-input-error :messages="$errors->get('nom_complet')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $user->email ?? '') }}" required autocomplete="off" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="cin" value="CIN" />
                        <x-text-input id="cin" name="cin" type="text" class="mt-1 block w-full" value="{{ old('cin', $user->cin ?? '') }}" autocomplete="new-password" data-lpignore="true" readonly onfocus="this.removeAttribute('readonly');" />
                        <x-input-error :messages="$errors->get('cin')" class="mt-2" />
                    </div>

                    
                    <div class="flex items-center mt-4 col-span-full">
                        <input id="est_actif" name="est_actif" type="checkbox" value="1" {{ old('est_actif', $user->est_actif ?? true) ? 'checked' : '' }} class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <label for="est_actif" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">Compte Actif</label>
                    </div>
                </div>
            </x-card>

            <div x-show="role === 'Employe_Standard' || role === 'Admin'" x-transition x-cloak>
                <div class="space-y-6">
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informations Employé</h3>
                        </x-slot>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="departement_id_employe" value="Département" />
                                <select id="departement_id_employe" name="departement_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" x-bind:disabled="role !== 'Employe_Standard' && role !== 'Admin'">
                                    <option value="">-- Sélectionner un département --</option>
                                    @foreach(\App\Models\Departement::all() as $dept)
                                        <option value="{{ $dept->id }}" {{ old('departement_id', $user->employe?->departement_id ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->nom_departement }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('departement_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="date_embauche" value="Date d'embauche" />
                                <x-text-input id="date_embauche" name="date_embauche" type="date" class="mt-1 block w-full" value="{{ old('date_embauche', $user->employe?->date_embauche?->format('Y-m-d') ?? '') }}" x-bind:disabled="role !== 'Employe_Standard' && role !== 'Admin'" />
                                <x-input-error :messages="$errors->get('date_embauche')" class="mt-2" />
                            </div>
                        </div>
                    </x-card>

                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Caractéristiques du Contrat</h3>
                        </x-slot>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="type_contrat" value="Type de Contrat" />
                                <select id="type_contrat" name="type_contrat" x-model="typeContrat" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" x-bind:disabled="role !== 'Employe_Standard' && role !== 'Admin'">
                                    <option value="CDI" {{ old('type_contrat', $contratActuel->type_contrat ?? '') == 'CDI' ? 'selected' : '' }}>CDI</option>
                                    <option value="CDD" {{ old('type_contrat', $contratActuel->type_contrat ?? '') == 'CDD' ? 'selected' : '' }}>CDD</option>
                                    <option value="Freelance" {{ old('type_contrat', $contratActuel->type_contrat ?? '') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                                </select>
                                <x-input-error :messages="$errors->get('type_contrat')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="date_debut" value="Date de Début" />
                                <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full" value="{{ old('date_debut', $contratActuel?->date_debut?->format('Y-m-d') ?? '') }}" x-bind:disabled="role !== 'Employe_Standard' && role !== 'Admin'" />
                                <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="date_fin">
                                    Date de Fin
                                    <span x-show="dateFinRequired" class="text-red-500 font-bold">*</span>
                                    <span x-show="!dateFinRequired" class="text-gray-400 text-xs font-normal">(Optionnelle)</span>
                                </x-input-label>
                                <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full" value="{{ old('date_fin', $contratActuel?->date_fin?->format('Y-m-d') ?? '') }}" x-bind:disabled="role !== 'Employe_Standard' && role !== 'Admin'" />
                                <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="salaire_base" value="Salaire de Base" />
                                <x-text-input id="salaire_base" name="salaire_base" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('salaire_base', $contratActuel->salaire_base ?? '') }}" x-bind:disabled="role !== 'Employe_Standard' && role !== 'Admin'" />
                                <x-input-error :messages="$errors->get('salaire_base')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="heures_hebdo" value="Heures Hebdomadaires" />
                                <x-text-input id="heures_hebdo" name="heures_hebdo" type="number" class="mt-1 block w-full" value="{{ old('heures_hebdo', $contratActuel->heures_hebdo ?? '') }}" x-bind:disabled="role !== 'Employe_Standard' && role !== 'Admin'" />
                                <x-input-error :messages="$errors->get('heures_hebdo')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="statut" value="Statut du Contrat" />
                                <select id="statut" name="statut" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" x-bind:disabled="role !== 'Employe_Standard' && role !== 'Admin'">
                                    <option value="actif" {{ old('statut', $contratActuel->statut ?? '') == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="suspendu" {{ old('statut', $contratActuel->statut ?? '') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                                    <option value="termine" {{ old('statut', $contratActuel->statut ?? '') == 'termine' ? 'selected' : '' }}>Terminé</option>
                                </select>
                                <x-input-error :messages="$errors->get('statut')" class="mt-2" />
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>

            <div x-show="role === 'Stagiaire'" x-transition x-cloak>
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informations Stagiaire</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="ecole_origine" value="École d'origine" />
                            <x-text-input id="ecole_origine" name="ecole_origine" type="text" class="mt-1 block w-full" value="{{ old('ecole_origine', $user->stagiaire?->ecole_origine ?? '') }}" x-bind:required="role === 'Stagiaire'" x-bind:disabled="role !== 'Stagiaire'" />
                            <x-input-error :messages="$errors->get('ecole_origine')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="departement_id_stagiaire" value="Département" />
                            <select id="departement_id_stagiaire" name="departement_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" x-bind:required="role === 'Stagiaire'" x-bind:disabled="role !== 'Stagiaire'">
                                <option value="">-- Sélectionner un département --</option>
                                @foreach(\App\Models\Departement::all() as $dept)
                                    <option value="{{ $dept->id }}" {{ old('departement_id', $user->stagiaire?->departement_id ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->nom_departement }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('departement_id')" class="mt-2" />
                        </div>
                        <div class="col-span-full">
                            <x-input-label for="sujet_stage" value="Sujet de stage" />
                            <textarea id="sujet_stage" name="sujet_stage" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" x-bind:required="role === 'Stagiaire'" x-bind:disabled="role !== 'Stagiaire'">{{ old('sujet_stage', $user->stagiaire?->sujet_stage ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('sujet_stage')" class="mt-2" />
                        </div>
                    </div>
                </x-card>
            </div>

            <div x-show="role === 'Client'" x-transition x-cloak>
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informations Client</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="type_client" value="Type de client" />
                            <select id="type_client" name="type_client" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" x-bind:required="role === 'Client'" x-bind:disabled="role !== 'Client'">
                                <option value="physique" {{ old('type_client', $user->client?->type_client ?? '') == 'physique' ? 'selected' : '' }}>Physique</option>
                                <option value="morale" {{ old('type_client', $user->client?->type_client ?? '') == 'morale' ? 'selected' : '' }}>Morale</option>
                            </select>
                            <x-input-error :messages="$errors->get('type_client')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="nom_societe" value="Nom Société (Optionnel)" />
                            <x-text-input id="nom_societe" name="nom_societe" type="text" class="mt-1 block w-full" :value="old('nom_societe', $user->client?->nom_societe ?? '')" x-bind:disabled="role !== 'Client'" />
                            <x-input-error :messages="$errors->get('nom_societe')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="ice" value="ICE (Optionnel)" />
                            <x-text-input id="ice" name="ice" type="text" class="mt-1 block w-full" :value="old('ice', $user->client?->ice ?? '')" x-bind:disabled="role !== 'Client'" />
                            <x-input-error :messages="$errors->get('ice')" class="mt-2" />
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="flex justify-end pt-4">
                <x-primary-button>
                    {{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }}
                </x-primary-button>
            </div>
        </div>
    </form>
</div>