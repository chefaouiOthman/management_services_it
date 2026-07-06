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
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Rôle</th>
                                <th scope="col" class="px-6 py-3">Type</th>
                                <th scope="col" class="px-6 py-3">Statut</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $user->nom_complet }}
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
