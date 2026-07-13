<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Modifier la Facture : {{ $facture->num_facture }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <form action="{{ route('factures.update', $facture->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="client_id" value="Client *" />
                            <select name="client_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm">
                                <option value="">-- Sélectionner un client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->user_id }}" @selected(old('client_id', $facture->client_id) == $client->user_id)>
                                        {{ $client->user?->nom_complet ?? 'Client sans compte' }}
                                        @if($client->nom_societe) ({{ $client->nom_societe }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('client_id')" />
                        </div>
                        <div>
                            <x-input-label for="num_facture" value="Numéro de Facture *" />
                            <x-text-input id="num_facture" name="num_facture" required class="mt-1 block w-full" :value="old('num_facture', $facture->num_facture)" />
                            <x-input-error class="mt-2" :messages="$errors->get('num_facture')" />
                        </div>
                        <div>
                            <x-input-label for="date_emission" value="Date d'Émission *" />
                            <x-text-input id="date_emission" type="date" name="date_emission" required class="mt-1 block w-full" :value="old('date_emission', $facture->date_emission?->format('Y-m-d') ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('date_emission')" />
                        </div>
                        <div>
                            <x-input-label for="statut_paiement" value="Statut *" />
                            <select name="statut_paiement" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm">
                                <option value="emise" @selected(old('statut_paiement', $facture->statut_paiement) === 'emise')>Émise</option>
                                <option value="en_retard_paiement" @selected(old('statut_paiement', $facture->statut_paiement) === 'en_retard_paiement')>En Retard de Paiement</option>
                                <option value="soldee" @selected(old('statut_paiement', $facture->statut_paiement) === 'soldee')>Soldée</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('statut_paiement')" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('factures.show', $facture->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 transition">Annuler</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 transition">Mettre à jour</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
