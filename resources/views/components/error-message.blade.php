@props(['message' => session('error')])

<div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-md">
    <p class="text-red-800 dark:text-red-200">{{ $message }}</p>
</div>
