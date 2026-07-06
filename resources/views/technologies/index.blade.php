<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Technologies
            </h2>
            <a href="{{ route('technologies.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold">
                + Ajouter
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Nom</th>
                            <th class="px-6 py-3">Version</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\App\Models\Technologie::all() as $tech)
                            <tr class="border-b last:border-0 hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $tech->id }}</td>
                                <td class="px-6 py-4 font-bold">{{ $tech->nom_tech }}</td>
                                <td class="px-6 py-4 font-mono">{{ $tech->version }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('technologies.destroy', $tech->id) }}" method="POST" onsubmit="return confirm('Supprimer ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-bold text-xs">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune technologie.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>
</x-app-layout>
