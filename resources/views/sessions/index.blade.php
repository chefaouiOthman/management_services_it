<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Sessions de Formation
            </h2>
            @can('session-formation-create')
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Planifier Session
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
                                <th scope="col" class="px-6 py-3">Programme</th>
                                <th scope="col" class="px-6 py-3">Dates</th>
                                <th scope="col" class="px-6 py-3">Formateurs</th>
                                <th scope="col" class="px-6 py-3">Lieux</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('sessions.show', $session->id) }}" class="text-indigo-600 hover:underline">
                                            {{ $session->catalogueFormation->titre_formation }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        Du {{ \Carbon\Carbon::parse($session->date_debut)->format('d/m/Y') }}<br>
                                        Au {{ \Carbon\Carbon::parse($session->date_fin)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($session->formateurs as $formateur)
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded border border-gray-200 dark:border-gray-600">
                                                    {{ $formateur->user->nom_complet }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($session->salle_concrete)
                                            <span class="block">📍 {{ $session->salle_concrete }}</span>
                                        @endif
                                        @if($session->salle_virtuelle)
                                            <a href="{{ $session->salle_virtuelle }}" target="_blank" class="text-indigo-600 hover:underline text-xs">🔗 Visio</a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('sessions.show', $session->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Hub</a>
                                        @can('session-formation-edit')
                                        <a href="{{ route('sessions.edit', $session->id) }}" class="text-gray-500 hover:text-gray-900 font-medium">Éditer</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Aucune session de formation planifiée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
