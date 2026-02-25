@props(['message' => session('success')])

<div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-md">
    <p class="text-green-800 dark:text-green-200">{{ $message }}</p>
</div>
