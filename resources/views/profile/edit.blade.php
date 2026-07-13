<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(auth()->user()->hasRole('Admin'))

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            @else

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <section>
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Informations personnelles') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __('Consultation de votre profil (lecture seule).') }}
                                </p>
                            </header>

                            <div class="mt-6 space-y-6">
                                <div>
                                    <x-input-label value="Nom complet" />
                                    <p class="mt-1 text-gray-900 font-medium">{{ $user->name }}</p>
                                </div>

                                <div>
                                    <x-input-label value="Email" />
                                    <p class="mt-1 text-gray-900 font-medium">{{ $user->email }}</p>
                                </div>

                                <div>
                                    <x-input-label value="Rôle" />
                                    <p class="mt-1 text-gray-900 font-medium">{{ $user->roles->pluck('name')->join(', ') }}</p>
                                </div>

                                @if($user->employe)
                                <div>
                                    <x-input-label value="Poste" />
                                    <p class="mt-1 text-gray-900 font-medium">{{ $user->employe->poste ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <x-input-label value="Date d'embauche" />
                                    <p class="mt-1 text-gray-900 font-medium">{{ $user->employe->date_embauche ? \Carbon\Carbon::parse($user->employe->date_embauche)->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                                @endif

                                @if($user->stagiaire)
                                <div>
                                    <x-input-label value="Niveau" />
                                    <p class="mt-1 text-gray-900 font-medium">{{ $user->stagiaire->niveau ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <x-input-label value="Date début" />
                                    <p class="mt-1 text-gray-900 font-medium">{{ $user->stagiaire->date_debut ? \Carbon\Carbon::parse($user->stagiaire->date_debut)->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                                @endif

                                @if($user->client)
                                <div>
                                    <x-input-label value="Société" />
                                    <p class="mt-1 text-gray-900 font-medium">{{ $user->client->societe ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <x-input-label value="Téléphone" />
                                    <p class="mt-1 text-gray-900 font-medium">{{ $user->client->telephone ?? 'N/A' }}</p>
                                </div>
                                @endif
                            </div>
                        </section>
                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
