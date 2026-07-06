<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nouvelle Feuille de Temps
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('feuille_temps.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="employe_id" value="Employé *" />
                        <select name="employe_id" required class="mt-1 block w-full border-gray-300 rounded-md">
                            @foreach(\App\Models\Employe::with('user')->get() as $emp)
                                <option value="{{ $emp->user_id }}">{{ $emp->user->nom_complet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="projet_id" value="Projet *" />
                        <select name="projet_id" required class="mt-1 block w-full border-gray-300 rounded-md">
                            @foreach(\App\Models\Projet::all() as $p)
                                <option value="{{ $p->id }}">{{ $p->nom_projet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="date_effort" value="Date *" />
                        <x-text-input type="date" name="date_effort" required class="mt-1 block w-full" value="{{ date('Y-m-d') }}" />
                    </div>
                    <div>
                        <x-input-label for="duree_heures" value="Durée (Heures) *" />
                        <x-text-input type="number" step="0.5" name="duree_heures" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="commentaire" value="Commentaire" />
                        <textarea name="commentaire" class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
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
