<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Inventaire MatÃ©riel IT
            </h2>
            @can('asset-create')
            @unless(auth()->user()->hasRole('Stagiaire') || auth()->user()->hasRole('Employe_Standard'))
            <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Ajouter Matériel
            </a>
            @endunless
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(auth()->user()->hasRole('Admin'))
            {{-- UX Instruction Banner --}}
            <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm text-blue-700">
                    <strong>Conseil :</strong> Pour assigner un matÃ©riel ou enregistrer une maintenance, veuillez d'abord cliquer sur le matÃ©riel concernÃ©.
                </p>
            </div>
            @endif

            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Ã‰quipement</th>
                                <th scope="col" class="px-6 py-3">ModÃ¨le & NÂ° SÃ©rie</th>
                                <th scope="col" class="px-6 py-3">Statut Actuel</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        <div class="flex flex-col">
                                            <a href="{{ route('assets.show', $asset->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-base font-semibold">
                                                {{ $asset->typeMateriel?->libelle_type ?? 'N/A' }} - {{ $asset->marque }}
                                            </a>
                                            <span class="text-xs text-gray-500">AjoutÃ© le {{ \Carbon\Carbon::parse($asset->date_achat_actif)->format('d/m/Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $asset->modele }}<br>
                                        <span class="text-xs font-mono text-gray-400">SN: {{ $asset->num_serie }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statutColor = match($asset->statut_materiel) {
                                                'disponible' => 'success',
                                                'attribue'   => 'info',
                                                'en_panne'   => 'danger',
                                                'reforme'    => 'gray',
                                                default      => 'gray',
                                            };
                                        @endphp
                                        <x-badge :type="$statutColor">
                                            {{ ucfirst(str_replace('_', ' ', $asset->statut_materiel)) }}
                                        </x-badge>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('assets.show', $asset->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">Ouvrir Hub</a>
                                        @can('asset-edit')
                                        @unless(auth()->user()->hasRole('Stagiaire') || auth()->user()->hasRole('Employe_Standard'))
                                        <a href="{{ route('assets.edit', $asset->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium ml-3">Éditer</a>
                                        @endunless
                                        @endcan
                                        @can('asset-delete')
                                        @if(auth()->user()->hasRole('Admin'))
                                        <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression définitive de ce matériel ?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium ml-2">Supprimer</button>
                                        </form>
                                        @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucun matÃ©riel dans l'inventaire.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
