@php
    $descriptionMap = [
        'view'  => 'Permet de consulter et lister les éléments de cette ressource.',
        'create' => 'Permet de créer de nouveaux éléments dans cette ressource.',
        'edit'   => 'Permet de modifier les éléments existants de cette ressource.',
        'delete' => 'Permet de supprimer les éléments de cette ressource.',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#1E293B] leading-tight tracking-tight">
                {{ __('Rôle : ') }} {{ $role->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-[#475569] bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
                    Retour
                </a>
                @can('role-edit')
                <a href="{{ route('roles.edit', $role->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-all duration-200">
                    Modifier
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-[#1E293B]">Informations du Rôle</h3>
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <x-input-label value="Nom du Rôle" />
                    <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $role->name }}</p>
                </div>
                <div>
                    <x-input-label value="Permissions Associées" />
                    <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $role->permissions->count() }}</p>
                </div>
                <div>
                    <x-input-label value="Date de création" />
                    <p class="mt-1 text-base font-medium text-[#1E293B]">{{ $role->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                </div>
            </div>
        </x-card>

        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-[#1E293B]">Permissions Attribuées</h3>
            </x-slot>
            @if($role->permissions->isNotEmpty())
                @php
                    $grouped = $role->permissions->groupBy(function($perm) {
                        return explode('-', $perm->name)[0] ?? $perm->name;
                    });
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($grouped as $resource => $perms)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <h4 class="text-sm font-semibold text-[#1E293B] uppercase tracking-wider mb-2">{{ $resource }}</h4>
                            <div class="space-y-1">
                                @foreach($perms as $perm)
                                    @php
                                        $action = explode('-', $perm->name)[1] ?? '';
                                        $desc = $descriptionMap[$action] ?? 'Permission système.';
                                    @endphp
                                    <div x-data="{ open: false }" class="flex items-center gap-1">
                                        <x-badge type="indigo" class="text-xs">{{ $action }}</x-badge>
                                        <button @click="open = !open" type="button" class="text-gray-400 hover:text-indigo-600 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="relative">
                                            <div class="absolute z-10 left-0 mt-1 w-64 px-3 py-2 text-xs text-white bg-gray-800 rounded-lg shadow-lg">
                                                {{ $desc }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Aucune permission attribuée à ce rôle.</p>
            @endif
        </x-card>
    </div>
</x-app-layout>
