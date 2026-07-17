<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.06),0_1px_2px_-1px_rgba(0,0,0,0.04)] border border-gray-100 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 ease-in-out']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            {{ $header }}
        </div>
    @endif
    <div class="p-6 text-[#1E293B]">
        {{ $slot }}
    </div>
    @if(isset($footer))
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $footer }}
        </div>
    @endif
</div>
