<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier un Pointage</h2>
            <a href="{{ route('pointages.index') }}" class="text-indigo-600 hover:underline text-sm">&larr; Retour à l'historique</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('pointages.update', $pointage->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    @can('pointage-create')
                    <div>
                        <x-input-label for="user_id" value="Employé" />
                        <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ old('user_id', $pointage->user_id) == $u->id ? 'selected' : '' }}>{{ $u->nom_complet }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
                    </div>
                    @endcan

                    <div>
                        <x-input-label for="date_jour" value="Date" />
                        <x-text-input id="date_jour" name="date_jour" type="date" class="mt-1 block w-full" value="{{ old('date_jour', $pointage->date_jour instanceof \Carbon\Carbon ? $pointage->date_jour->format('Y-m-d') : $pointage->date_jour) }}" required />
                        <x-input-error :messages="$errors->get('date_jour')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="heure_arrivee" value="Heure d'arrivée" />
                        <x-text-input id="heure_arrivee" name="heure_arrivee" type="datetime-local" class="mt-1 block w-full" value="{{ old('heure_arrivee', $pointage->heure_arrivee ? \Carbon\Carbon::parse($pointage->heure_arrivee)->format('Y-m-d\TH:i') : '') }}" required />
                        <x-input-error :messages="$errors->get('heure_arrivee')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="heure_depart" value="Heure de départ (optionnelle)" />
                        <x-text-input id="heure_depart" name="heure_depart" type="datetime-local" class="mt-1 block w-full" value="{{ old('heure_depart', $pointage->heure_depart ? \Carbon\Carbon::parse($pointage->heure_depart)->format('Y-m-d\TH:i') : '') }}" />
                        <x-input-error :messages="$errors->get('heure_depart')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="statut_presence" value="Statut de présence" />
                        <select id="statut_presence" name="statut_presence" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="a_l_heure" {{ old('statut_presence', $pointage->statut_presence) == 'a_l_heure' ? 'selected' : '' }}>À l'heure</option>
                            <option value="en_retard" {{ old('statut_presence', $pointage->statut_presence) == 'en_retard' ? 'selected' : '' }}>En retard</option>
                            <option value="depart_anticipe" {{ old('statut_presence', $pointage->statut_presence) == 'depart_anticipe' ? 'selected' : '' }}>Départ anticipé</option>
                        </select>
                        <x-input-error :messages="$errors->get('statut_presence')" class="mt-1" />
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-primary-button>Mettre à jour le pointage</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
