@props(['routeName', 'params', 'label' => 'Debug Route'])

<div class="fixed bottom-4 right-4 z-50 bg-gray-900 text-white text-xs p-3 rounded-lg shadow-lg max-w-sm">
    <div class="font-semibold mb-1">{{ $label }}</div>
    <div class="space-y-1">
        <div><strong>Route:</strong> {{ $routeName }}</div>
        <div><strong>Params:</strong> <pre class="bg-gray-800 p-1 rounded">{{ json_encode($params, JSON_PRETTY_PRINT) }}</pre></div>
        <div><strong>Generated URL:</strong> 
            @try
                <span class="text-green-400">{{ route($routeName, $params) }}</span>
            @catch(\Exception $e)
                <span class="text-red-400">❌ ERROR: {{ $e->getMessage() }}</span>
            @endtry
        </div>
    </div>
    <button onclick="this.parentElement.remove()" class="mt-2 text-gray-400 hover:text-white text-xs">✕ Close</button>
</div>