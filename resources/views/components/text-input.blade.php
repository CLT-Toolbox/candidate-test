@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-600 bg-gray-700 text-white placeholder-gray-500 focus:border-indigo-600 focus:ring-indigo-600 rounded-md shadow-sm']) }}>
