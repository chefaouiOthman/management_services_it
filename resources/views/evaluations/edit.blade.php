<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Modifier l'Évaluation
            </h2>
            <a href="{{ route('evaluations.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                ← Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('evaluations.update', $evaluation->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Session & Intervenants</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="session_formation_id" value="Session de Formation *" />
                            <select id="session_formation_id" name="session_formation_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Choisir une session --</option>
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}" {{ old('session_formation_id', $evaluation->session_formation_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->catalogueFormation->titre_formation ?? 'N/A' }} ({{ $s->date_debut?->format('d/m/Y') ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('session_formation_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="user_id" value="Évaluateur (Utilisateur) *" />
                            <select id="user_id" name="user_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Choisir un utilisateur --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id', $evaluation->user_id) == $u->id ? 'selected' : '' }}>
                                        {{ $u->nom_complet }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="employe_id" value="Formateur noté *" />
                            <select id="employe_id" name="employe_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Choisir un formateur --</option>
                                @foreach($formateurs as $f)
                                    <option value="{{ $f->user_id }}" {{ old('employe_id', $evaluation->employe_id) == $f->user_id ? 'selected' : '' }}>
                                        {{ $f->user?->nom_complet ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('employe_id')" class="mt-2" />
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Notes (sur 5)</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="note_pedagogie" value="Note Pédagogie *" />
                            <x-text-input id="note_pedagogie" name="note_pedagogie" type="number" min="0" max="5" step="1" class="mt-1 block w-full" value="{{ old('note_pedagogie', $evaluation->note_pedagogie) }}" required />
                            <x-input-error :messages="$errors->get('note_pedagogie')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="note_technique" value="Note Technique *" />
                            <x-text-input id="note_technique" name="note_technique" type="number" min="0" max="5" step="1" class="mt-1 block w-full" value="{{ old('note_technique', $evaluation->note_technique) }}" required />
                            <x-input-error :messages="$errors->get('note_technique')" class="mt-2" />
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Avis textuel</h3>
                    </x-slot>
                    <div>
                        <textarea id="avis_textuel" name="avis_textuel" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">{{ old('avis_textuel', $evaluation->avis_textuel) }}</textarea>
                        <x-input-error :messages="$errors->get('avis_textuel')" class="mt-2" />
                    </div>
                </x-card>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('evaluations.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
