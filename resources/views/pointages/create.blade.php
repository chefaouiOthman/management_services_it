<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Saisir un Pointage Manuel</h2>
            <a href="{{ route('pointages.index') }}" class="text-indigo-600 hover:underline text-sm">&larr; Retour à l'historique</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('pointages.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="user_id" value="Employé" />
                        <select id="user_id" name="user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Sélectionner un employé</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->nom_complet }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="date_jour" value="Date" />
                        <x-text-input id="date_jour" name="date_jour" type="date" class="mt-1 block w-full" value="{{ old('date_jour', date('Y-m-d')) }}" required />
                        <x-input-error :messages="$errors->get('date_jour')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="heure_arrivee" value="Heure d'arrivée" />
                        <x-text-input id="heure_arrivee" name="heure_arrivee" type="datetime-local" class="mt-1 block w-full" value="{{ old('heure_arrivee') }}" required />
                        <x-input-error :messages="$errors->get('heure_arrivee')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="heure_depart" value="Heure de départ (optionnelle)" />
                        <x-text-input id="heure_depart" name="heure_depart" type="datetime-local" class="mt-1 block w-full" value="{{ old('heure_depart') }}" />
                        <x-input-error :messages="$errors->get('heure_depart')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="statut_presence" value="Statut de présence" />
                        <select id="statut_presence" name="statut_presence" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="a_l_heure" {{ old('statut_presence') == 'a_l_heure' ? 'selected' : '' }}>À l'heure</option>
                            <option value="en_retard" {{ old('statut_presence') == 'en_retard' ? 'selected' : '' }}>En retard</option>
                            <option value="depart_anticipe" {{ old('statut_presence') == 'depart_anticipe' ? 'selected' : '' }}>Départ anticipé</option>
                        </select>
                        <x-input-error :messages="$errors->get('statut_presence')" class="mt-1" />
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-primary-button>Enregistrer le pointage</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
