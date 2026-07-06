<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Ticket #{{ $ticket->id }}
            </h2>
            <div class="flex gap-2">
                @can('ticket-edit')
                <a href="{{ route('ticket_maintenances.edit', $ticket->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    Modifier Ticket
                </a>
                @endcan
                <a href="{{ route('ticket_maintenances.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition">
                    ← Retour au Helpdesk
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <div class="flex justify-between items-start border-b border-gray-200 pb-4 mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Incident sur {{ $ticket->assetMateriel->marque }} {{ $ticket->assetMateriel->modele }}</h3>
                        <p class="text-sm text-gray-500">S/N: {{ $ticket->assetMateriel->num_serie }}</p>
                        <p class="text-xs text-gray-400 mt-1">Signalé par <span class="font-medium text-gray-700">{{ $ticket->user->nom_complet }}</span> le {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <x-badge type="{{ $ticket->statut_ticket === 'resolu' ? 'success' : ($ticket->statut_ticket === 'en_atelier' ? 'warning' : 'danger') }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->statut_ticket)) }}
                        </x-badge>
                    </div>
                </div>

                <div class="prose max-w-none mb-6">
                    <h4 class="text-md font-semibold text-gray-700">Description de la Panne</h4>
                    <div class="p-4 bg-gray-50 rounded-lg text-gray-800 italic mt-2 border border-gray-100">
                        "{!! nl2br(e($ticket->description_panne)) !!}"
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500">Coût de la réparation :</p>
                        <p class="font-mono text-xl font-bold {{ $ticket->cout_reparation > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($ticket->cout_reparation, 2, ',', ' ') }} DHS</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
