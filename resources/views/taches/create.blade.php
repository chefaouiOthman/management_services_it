<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nouvelle Tâche
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('taches.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="titre_tache" value="Titre de la tâche *" />
                        <x-text-input name="titre_tache" required class="mt-1 block w-full" />
                    </div>
                    
                    <div>
                        <x-input-label for="projet_id" value="Projet (Optionnel)" />
                        <select name="projet_id" class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="">-- Independent Task (No Project Link) --</option>
                            @foreach(\App\Models\Projet::all() as $p)
                                <option value="{{ $p->id }}">{{ $p->nom_projet }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Si vous liez un projet, vous devez définir la priorité et le statut.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="priorite" value="Priorité" />
                            <select name="priorite" class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="basse">Basse</option>
                                <option value="moyenne" selected>Moyenne</option>
                                <option value="haute">Haute</option>
                                <option value="bloquante">Bloquante</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="statut_tache" value="Statut" />
                            <select name="statut_tache" class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="backlog" selected>Backlog</option>
                                <option value="en_cours">En Cours</option>
                                <option value="en_revue">En Revue</option>
                                <option value="termine">Terminé</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Enregistrer</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
