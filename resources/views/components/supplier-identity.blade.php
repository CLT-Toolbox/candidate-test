@props([
    'supplier',
    'href' => null,
])

@php
    $url = $href ?? route('suppliers.show', $supplier);
    $name = $supplier->name ?? '';
    $words = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);
    if (count($words) >= 2) {
        $initials = mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1));
    } elseif (count($words) === 1) {
        $w = $words[0];
        $initials = mb_strtoupper(mb_strlen($w) >= 2 ? mb_substr($w, 0, 2) : $w.mb_substr($w, 0, 1));
    } else {
        $initials = '?';
    }
@endphp

<a href="{{ $url }}" {{ $attributes->merge(['class' => 'flex items-center gap-3 min-w-0 max-w-md group']) }}>
    <span
        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sky-100 text-[0.7rem] font-bold leading-none text-blue-900 shadow-sm ring-1 ring-sky-200/90">
        {{ $initials }}
    </span>
    <span class="min-w-0 flex flex-col text-left">
        <span class="text-[0.95rem] font-semibold text-gray-900 group-hover:text-blue-800 truncate">
            {{ $name ?: '—' }}
        </span>
        <span class="text-xs font-sans font-normal text-gray-500 truncate">
            {{ __('ID:') }} {{ $supplier->supplier_id }}
        </span>
    </span>
</a>
