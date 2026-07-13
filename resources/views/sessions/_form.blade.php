@php
    $isEdit = isset($session) && $session->exists;
    $actionUrl = $isEdit ? route('sessions.update', $session->id) : route('sessions.store');
@endphp

<form method="POST" action="{{ $actionUrl }}" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">DÃ©tails de la Session</h3>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Catalogue -->
            <div class="md:col-span-2">
                <x-input-label for="catalogue_formation_id" value="Programme de Formation *" />
                <select id="catalogue_formation_id" name="catalogue_formation_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                    <option value="">-- Choisir un programme --</option>
                    @foreach($catalogues as $cat)
                        <option value="{{ $cat->id }}" @selected(old('catalogue_formation_id', $session->catalogue_formation_id ?? '') == $cat->id)>
                            {{ $cat->titre_formation }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('catalogue_formation_id')" />
            </div>

            <!-- Dates -->
            <div>
                <x-input-label for="date_debut" value="Date de DÃ©but *" />
                <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full" :value="old('date_debut', ($isEdit ? $session->date_debut?->format('Y-m-d') : ''))" required />
                <x-input-error class="mt-2" :messages="$errors->get('date_debut')" />
            </div>
            
            <div>
                <x-input-label for="date_fin" value="Date de Fin *" />
                <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full" :value="old('date_fin', ($isEdit ? $session->date_fin?->format('Y-m-d') : ''))" required />
                <x-input-error class="mt-2" :messages="$errors->get('date_fin')" />
            </div>

            <!-- Salles -->
            <div>
                <x-input-label for="salle_concrete" value="Salle Physique (optionnel)" />
                <x-text-input id="salle_concrete" name="salle_concrete" type="text" class="mt-1 block w-full" :value="old('salle_concrete', $session->salle_concrete ?? '')" placeholder="Ex: Salle A1" />
                <x-input-error class="mt-2" :messages="$errors->get('salle_concrete')" />
            </div>

            <div>
                <x-input-label for="salle_virtuelle" value="Lien Salle Virtuelle (optionnel)" />
                <x-text-input id="salle_virtuelle" name="salle_virtuelle" type="url" class="mt-1 block w-full" :value="old('salle_virtuelle', $session->salle_virtuelle ?? '')" placeholder="https://zoom.us/..." />
                <x-input-error class="mt-2" :messages="$errors->get('salle_virtuelle')" />
            </div>

            <!-- Formateurs (Pivot) -->
            <div class="md:col-span-2">
                <x-input-label value="Formateurs AssignÃ©s (EmployÃ©s)" />
                <p class="text-sm text-gray-500 mb-2">Cochez les employÃ©s qui animeront cette session.</p>
                <div class="max-h-60 overflow-y-auto border border-gray-300 rounded p-2 bg-white">
                    @php
                        $selectedFormateurs = old('formateurs', $isEdit ? $session->formateurs?->pluck('user_id')->toArray() ?? [] : []);
                    @endphp
                    @foreach($employes as $employe)
                        <label class="flex items-center space-x-2 p-1 hover:bg-gray-50">
                            <input type="checkbox" name="formateurs[]" value="{{ $employe->user_id }}" 
                                   @checked(in_array($employe->user_id, $selectedFormateurs))
                                   class="rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700">{{ $employe->user?->nom_complet ?? 'EmployÃ© sans compte' }} ({{ $employe->user?->email ?? 'N/A' }})</span>
                        </label>
                    @endforeach
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('formateurs')" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('sessions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 shadow-sm hover:bg-gray-50 transition">
                Annuler
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                {{ $isEdit ? 'Mettre Ã  jour' : 'Planifier' }}
            </button>
        </div>
    </x-card>
</form>
