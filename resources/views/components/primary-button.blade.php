<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:from-indigo-500 hover:to-violet-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:ring-offset-2 active:scale-[0.98] transition-all duration-150 shadow-sm']) }}>
    {{ $slot }}
</button>
