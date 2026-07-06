<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            {{ $header }}
        </div>
    @endif
    <div class="p-6 text-gray-900 dark:text-gray-100">
        {{ $slot }}
    </div>
    @if(isset($footer))
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            {{ $footer }}
        </div>
    @endif
</div>
