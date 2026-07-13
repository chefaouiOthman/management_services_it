<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nouvelle Inscription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('inscriptions.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour aux inscriptions
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('inscriptions.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-full">
                                <x-input-label for="user_id" value="Apprenant" />
                                <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required {{ !Auth::user()->hasRole('Admin') ? 'disabled' : '' }}>
                                    <option value="">-- Sélectionner un utilisateur --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (old('user_id', Auth::id()) == $user->id) ? 'selected' : '' }}>
                                            {{ $user->nom_complet }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @if(!Auth::user()->hasRole('Admin'))
                                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                                @endif
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                            </div>

                            <div class="col-span-full">
                                <x-input-label for="session_formation_id" value="Session de Formation" />
                                <select id="session_formation_id" name="session_formation_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- Sélectionner une session --</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('session_formation_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->catalogueFormation?->titre_formation ?? 'Sans titre' }} 
                                            (Du {{ \Carbon\Carbon::parse($session->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($session->date_fin)->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('session_formation_id')" class="mt-2" />
                            </div>

                            <div class="col-span-full">
                                <x-input-label for="statut_inscription" value="Statut Initial" />
                                <select id="statut_inscription" name="statut_inscription" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                    <option value="valide" {{ old('statut_inscription') == 'valide' ? 'selected' : '' }}>Validé</option>
                                    <option value="annule" {{ old('statut_inscription') == 'annule' ? 'selected' : '' }}>Annulé</option>
                                    <option value="present" {{ old('statut_inscription') == 'present' ? 'selected' : '' }}>Présent</option>
                                    <option value="certifie" {{ old('statut_inscription') == 'certifie' ? 'selected' : '' }}>Certifié</option>
                                </select>
                                <x-input-error :messages="$errors->get('statut_inscription')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <x-primary-button class="ml-4">
                                {{ __('Inscrire') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
