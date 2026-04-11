@if (session('success'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        {{ session('success') }}
    </div>
@endif

@if (session('warning'))
    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        {{ session('warning') }}
    </div>
@endif

@if ($errors->any())
    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        <p class="font-semibold">Please review the following issues:</p>
        <ul class="mt-2 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
