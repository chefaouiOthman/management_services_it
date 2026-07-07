<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Annuaire & RH (Hub Utilisateurs)') }}
            </h2>
            @can('user-create')
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Ajouter un Profil
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nom Complet</th>
                                <th scope="col" class="px-6 py-3">CIN</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Rôle</th>
                                <th scope="col" class="px-6 py-3">Type</th>
                                <th scope="col" class="px-6 py-3">Statut</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr x-data="{ detailsOpen: false }" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $user->nom_complet }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-gray-600">{{ $user->cin ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @foreach($user->roles as $role)
                                            <x-badge type="gray">{{ $role->name }}</x-badge>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->employe)
                                            <x-badge type="info">Employé</x-badge>
                                        @elseif($user->stagiaire)
                                            <x-badge type="warning">Stagiaire</x-badge>
                                        @elseif($user->client)
                                            <x-badge type="success">Client</x-badge>
                                        @else
                                            <span class="text-gray-400 italic">Aucun</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->est_actif)
                                            <x-badge type="success">Actif</x-badge>
                                        @else
                                            <x-badge type="danger">Inactif</x-badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="detailsOpen = true" class="font-medium text-blue-600 dark:text-blue-500 hover:underline mr-3">Voir les détails</button>
                                        @can('user-edit')
                                            <a href="{{ route('users.edit', $user->id) }}" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline mr-3">Modifier</a>
                                        @endcan
                                        @can('user-delete')
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">Supprimer</button>
                                            </form>
                                        @endcan
                                    </td>
                                    
                                    <!-- Modale Détails Polymorphiques -->
                                    <td x-show="detailsOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" @click.self="detailsOpen = false">
                                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-lg mx-auto text-left">
                                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                                    Détails : {{ $user->nom_complet }}
                                                </h3>
                                                <button @click="detailsOpen = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
                                            </div>
                                            
                                            <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div><strong>Email :</strong> {{ $user->email }}</div>
                                                    <div><strong>CIN :</strong> {{ $user->cin ?? 'Non renseigné' }}</div>
                                                </div>
                                                <hr class="border-gray-200 dark:border-gray-700">
                                                
                                                @if($user->employe)
                                                    <h4 class="font-bold text-lg text-indigo-600">Profil Employé</h4>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div><strong>Date d'embauche :</strong> {{ $user->employe->date_embauche ? \Carbon\Carbon::parse($user->employe->date_embauche)->format('d/m/Y') : '-' }}</div>
                                                        <div><strong>Département :</strong> {{ $user->employe->departement->nom_departement ?? 'Non assigné' }}</div>
                                                        <div><strong>CIN Employé :</strong> {{ $user->employe->CIN ?? '-' }}</div>
                                                    </div>
                                                    @if($user->employe->contrats->isNotEmpty())
                                                        @php $contrat = $user->employe->contrats->first(); @endphp
                                                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-600">
                                                            <p class="text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Contrat Actuel</p>
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                                                <div class="flex flex-col">
                                                                    <span class="text-xs text-gray-400 uppercase tracking-wide">Type</span>
                                                                    <span class="mt-1 px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded text-xs font-bold w-fit">{{ $contrat->type_contrat ?? 'N/A' }}</span>
                                                                </div>
                                                                <div class="flex flex-col">
                                                                    <span class="text-xs text-gray-400 uppercase tracking-wide">Statut</span>
                                                                    <span class="mt-1 px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs font-bold capitalize w-fit">{{ $contrat->statut ?? 'N/A' }}</span>
                                                                </div>
                                                                <div class="flex flex-col">
                                                                    <span class="text-xs text-gray-400 uppercase tracking-wide">Salaire de base</span>
                                                                    <span class="mt-1 font-mono font-bold text-gray-900 dark:text-white">{{ $contrat->salaire_base ? number_format($contrat->salaire_base, 2, ',', ' ') . ' DHS' : 'N/A' }}</span>
                                                                </div>
                                                                <div class="flex flex-col">
                                                                    <span class="text-xs text-gray-400 uppercase tracking-wide">Heures / semaine</span>
                                                                    <span class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $contrat->heures_hebdo ? $contrat->heures_hebdo . 'h' : 'N/A' }}</span>
                                                                </div>
                                                                <div class="flex flex-col">
                                                                    <span class="text-xs text-gray-400 uppercase tracking-wide">Date de début</span>
                                                                    <span class="mt-1 text-gray-800 dark:text-gray-200">{{ $contrat->date_debut ? \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') : 'N/A' }}</span>
                                                                </div>
                                                                <div class="flex flex-col">
                                                                    <span class="text-xs text-gray-400 uppercase tracking-wide">Date de fin</span>
                                                                    <span class="mt-1 text-gray-800 dark:text-gray-200">{{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminée' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <p class="text-xs italic text-gray-400 mt-2">Aucun contrat enregistré.</p>
                                                    @endif
                                                @elseif($user->stagiaire)
                                                    <h4 class="font-bold text-lg text-yellow-600">Profil Stagiaire</h4>
                                                    <div class="space-y-2">
                                                        <div><strong>École d'origine :</strong> {{ $user->stagiaire->ecole_origine ?? '-' }}</div>
                                                        <div><strong>Sujet de stage :</strong> {{ $user->stagiaire->sujet_stage ?? '-' }}</div>
                                                        <div><strong>Département :</strong> {{ $user->stagiaire->departement->nom_departement ?? 'Non assigné' }}</div>
                                                    </div>
                                                @elseif($user->client)
                                                    <h4 class="font-bold text-lg text-green-600">Profil Client</h4>
                                                    <div class="space-y-2">
                                                        <div><strong>Type :</strong> <span class="capitalize">{{ $user->client->type_client ?? '-' }}</span></div>
                                                        <div><strong>Société :</strong> {{ $user->client->nom_societe ?? '-' }}</div>
                                                        <div><strong>ICE :</strong> {{ $user->client->ice ?? '-' }}</div>
                                                    </div>
                                                @else
                                                    <p class="text-gray-500 italic">Cet utilisateur n'est lié à aucune entité métier (ni Employé, ni Stagiaire, ni Client).</p>
                                                @endif
                                            </div>
                                            
                                            <div class="mt-6 flex justify-end">
                                                <button @click="detailsOpen = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md font-medium transition">Fermer</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Aucun utilisateur trouvé.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 px-4 pb-4">
                    {{ $users->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
