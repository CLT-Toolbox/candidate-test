@props(['disabled' => false])

<input
    type="file"
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-gray-50 dark:text-gray-300 focus:outline-none dark:bg-gray-900 dark:border-gray-700 dark:placeholder-gray-400
        file:mr-4 file:py-2 file:px-4
        file:rounded-l-md file:border-0
        file:text-sm file:font-semibold
        file:bg-indigo-50 file:text-indigo-700
        hover:file:bg-indigo-100
        dark:file:bg-gray-700 dark:file:text-gray-300
        disabled:opacity-50 disabled:cursor-not-allowed'
    ]) }}
>
