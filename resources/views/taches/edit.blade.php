<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifier Tâche
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('taches.update', $tache->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="titre_tache" value="Titre de la tâche *" />
                        <x-text-input name="titre_tache" value="{{ $tache->titre_tache }}" required class="mt-1 block w-full" />
                    </div>
                    
                    <div>
                        <x-input-label for="projet_id" value="Projet (Optionnel)" />
                        <select name="projet_id" class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="">-- Independent Task (No Project Link) --</option>
                            @foreach($projets as $p)
                                <option value="{{ $p->id }}" {{ $currentProjet && $currentProjet->id == $p->id ? 'selected' : '' }}>{{ $p->nom_projet }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Si vous liez un projet, vous devez définir la priorité et le statut.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="priorite" value="Priorité" />
                            <select name="priorite" class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="basse" {{ $currentProjet && $currentProjet->pivot->priorite == 'basse' ? 'selected' : '' }}>Basse</option>
                                <option value="moyenne" {{ !$currentProjet || $currentProjet->pivot->priorite == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                <option value="haute" {{ $currentProjet && $currentProjet->pivot->priorite == 'haute' ? 'selected' : '' }}>Haute</option>
                                <option value="bloquante" {{ $currentProjet && $currentProjet->pivot->priorite == 'bloquante' ? 'selected' : '' }}>Bloquante</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="statut_tache" value="Statut" />
                            <select name="statut_tache" class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="backlog" {{ !$currentProjet || $currentProjet->pivot->statut_tache == 'backlog' ? 'selected' : '' }}>Backlog</option>
                                <option value="en_cours" {{ $currentProjet && $currentProjet->pivot->statut_tache == 'en_cours' ? 'selected' : '' }}>En Cours</option>
                                <option value="en_revue" {{ $currentProjet && $currentProjet->pivot->statut_tache == 'en_revue' ? 'selected' : '' }}>En Revue</option>
                                <option value="termine" {{ $currentProjet && $currentProjet->pivot->statut_tache == 'termine' ? 'selected' : '' }}>Terminé</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Mettre à jour</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
