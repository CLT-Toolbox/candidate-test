<div class="bg-white rounded-2xl border border-gray-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden min-h-[400px]">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse']) }}>
        @if(isset($thead))
            <thead class="bg-gray-50/50">
                <tr class="border-b border-gray-200">
                    {{ $thead }}
                </tr>
            </thead>
        @endif
        
        <tbody class="divide-y divide-gray-200">
            {{ $slot }}
        </tbody>
    </table>

    @if(isset($footer))
        <div class="px-8 py-4 border-t border-gray-200 bg-gray-50/50">
            {{ $footer }}
        </div>
    @endif
</div>
