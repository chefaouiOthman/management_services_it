@props([
    'search' => '',
    'searchPlaceholder' => 'Rechercher...',
    'filters' => [],
    'route' => null,
])

<div x-data="{ search: '{{ $search }}' }" class="mb-6">
    <form method="GET" action="{{ $route ?? request()->url() }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Recherche</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" x-model="search" value="{{ $search }}"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full pl-10 pr-4 py-2.5 border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 rounded-xl text-sm transition-all duration-200">
            </div>
        </div>

        @foreach($filters as $key => $filter)
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">{{ $filter['label'] }}</label>
                @if(isset($filter['type']) && $filter['type'] === 'date')
                    <input type="date" name="{{ $key }}" value="{{ request($key) }}"
                        class="w-full px-4 py-2.5 border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 rounded-xl text-sm transition-all duration-200">
                @else
                    <select name="{{ $key }}"
                        class="w-full px-4 py-2.5 border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 rounded-xl text-sm transition-all duration-200">
                        <option value="">Tous</option>
                        @foreach($filter['options'] as $val => $label)
                            <option value="{{ $val }}" {{ request($key) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endforeach

        <div class="flex items-center gap-2 pb-0.5">
            <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-500 hover:shadow-lg hover:shadow-indigo-500/25 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all duration-300 ease-in-out shadow-sm">
                Filtrer
            </button>
            @if(request()->anyFilled(array_merge(['search'], array_keys($filters))))
                <a href="{{ $route ?? request()->url() }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 hover:scale-[1.02] transition-all duration-300 ease-in-out">
                    Réinitialiser
                </a>
            @endif
        </div>
    </form>
</div>
