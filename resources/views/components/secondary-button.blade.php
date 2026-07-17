<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-wider shadow-sm hover:bg-gray-50 hover:border-gray-300 hover:shadow-lg hover:shadow-gray-200/50 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:ring-offset-2 disabled:opacity-25 active:scale-[0.98] transition-all duration-300 ease-in-out']) }}>
    {{ $slot }}
</button>
