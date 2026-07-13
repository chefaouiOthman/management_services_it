<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Types de Matériel
            </h2>
            @can('type-materiel-create')
            @unless(auth()->user()->hasRole('Stagiaire') || auth()->user()->hasRole('Employe_Standard'))
            <a href="{{ route('type_materiels.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Ajouter un type
            </a>
            @endunless
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Titre</th>
                                <th scope="col" class="px-6 py-3">Description</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $type)
                                <tr class="bg-white border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        #{{ $type->id }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $type->libelle_type }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ Str::limit($type->description_type, 80) }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end items-center gap-3">
                                            @can('type-materiel-edit')
                                            @unless(auth()->user()->hasRole('Stagiaire') || auth()->user()->hasRole('Employe_Standard'))
                                            <a href="{{ route('type_materiels.edit', $type->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Modifier</a>
                                            @endunless
                                            @endcan
                                            @can('type-materiel-delete')
                                            @unless(auth()->user()->hasRole('Stagiaire') || auth()->user()->hasRole('Employe_Standard'))
                                            <form action="{{ route('type_materiels.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce type de matériel ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Supprimer</button>
                                            </form>
                                            @endunless
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucun type de matériel enregistré.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
