<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($ticket) ? 'Éditer le Ticket #' . $ticket->id : 'Signaler un Incident' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ isset($ticket) ? route('tickets.update', $ticket->id) : route('tickets.store') }}" method="POST" class="space-y-6">
                    @csrf
                    @if(isset($ticket))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if(Auth::user()->hasRole('Admin'))
                            <div>
                                <x-input-label for="user_id" value="Demandeur *" />
                                <select name="user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500">
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" @selected(old('user_id', $ticket->user_id ?? Auth::id()) == $u->id)>{{ $u->nom_complet }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                            </div>
                        @else
                            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        @endif

                        <div>
                            <x-input-label for="asset_materiel_id" value="Matériel concerné *" />
                            @php
                                $preselectedAssetId = request('asset_id');
                                $isLocked = !isset($ticket) && $preselectedAssetId;
                            @endphp
                            <select name="{{ $isLocked ? '_ignore_asset' : 'asset_materiel_id' }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 {{ $isLocked ? 'bg-gray-100 opacity-75' : '' }}" {{ $isLocked ? 'disabled' : '' }}>
                                <option value="">-- Sélectionner --</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" @selected(old('asset_materiel_id', $ticket->asset_materiel_id ?? $preselectedAssetId) == $asset->id)>
                                        {{ $asset->marque }} {{ $asset->modele }} (SN: {{ $asset->num_serie }})
                                    </option>
                                @endforeach
                            </select>
                            @if($isLocked)
                                <input type="hidden" name="asset_materiel_id" value="{{ $preselectedAssetId }}">
                            @endif
                            <x-input-error class="mt-2" :messages="$errors->get('asset_materiel_id')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="description_panne" value="Description de la Panne *" />
                            <textarea name="description_panne" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500">{{ old('description_panne', $ticket->description_panne ?? '') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description_panne')" />
                        </div>

                        @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Manager'))
                            <div>
                                <x-input-label for="statut_ticket" value="Statut du Ticket *" />
                                <select name="statut_ticket" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500">
                                    <option value="signale" @selected(old('statut_ticket', $ticket->statut_ticket ?? '') === 'signale')>Signalé</option>
                                    <option value="en_atelier" @selected(old('statut_ticket', $ticket->statut_ticket ?? '') === 'en_atelier')>En Atelier</option>
                                    <option value="resolu" @selected(old('statut_ticket', $ticket->statut_ticket ?? '') === 'resolu')>Résolu</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('statut_ticket')" />
                            </div>
                            <div>
                                <x-input-label for="cout_reparation" value="Coût de la réparation (DHS)" />
                                <x-text-input id="cout_reparation" name="cout_reparation" type="number" step="0.01" class="mt-1 block w-full" :value="old('cout_reparation', $ticket->cout_reparation ?? '0')" />
                                <x-input-error class="mt-2" :messages="$errors->get('cout_reparation')" />
                            </div>
                        @else
                            <input type="hidden" name="statut_ticket" value="{{ $ticket->statut_ticket ?? 'signale' }}">
                            <input type="hidden" name="cout_reparation" value="{{ $ticket->cout_reparation ?? '0' }}">
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('tickets.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 transition">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 transition">Enregistrer</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
