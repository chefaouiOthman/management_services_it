<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Modification Session : {{ $session->catalogueFormation->titre_formation }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @include('sessions._form', ['session' => $session])
        </div>
    </div>
</x-app-layout>
