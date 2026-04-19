@props(['items' => []])

<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach($items as $label => $url)
            <li class="inline-flex items-center">
                @if(!$loop->first)
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 mx-2"></i>
                @endif
                
                @if($url)
                    <a href="{{ $url }}" class="text-[10px] font-bold uppercase tracking-widest text-gray-500 hover:text-brand-600 transition-colors">
                        {{ $label }}
                    </a>
                @else
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
                        {{ $label }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
