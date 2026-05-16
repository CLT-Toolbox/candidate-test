import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './app/**/*.php',
        './routes/**/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],

    // Safelist for dynamically generated classes in JavaScript
    safelist: [
        // Background colors
        'bg-white', 'bg-gray-50', 'bg-gray-100', 'bg-gray-200', 'bg-green-50', 'bg-green-100', 'bg-green-600', 'bg-blue-50', 'bg-blue-100', 'bg-red-50',
        // Text colors
        'text-xs', 'text-sm', 'text-lg', 'text-gray-500', 'text-gray-700', 'text-gray-900', 'text-green-600', 'text-blue-800', 'text-red-600',
        // Border styles
        'border', 'border-2', 'border-b', 'border-t', 'border-gray-200', 'border-gray-300', 'border-gray-600', 'border-green-500', 'border-green-600', 'border-blue-500', 'border-red-600',
        // Padding
        'px-2', 'px-3', 'px-4', 'py-1', 'py-2', 'py-3', 'py-4', 'py-6', 'py-8', 'pt-6', 'pb-4',
        // Margin
        'mb-3', 'mb-4', 'mb-6', 'mt-2', 'mt-4', 'mt-6', 'mx-auto', 'mr-2',
        // Sizing
        'w-4', 'w-5', 'w-full', 'w-72', 'h-4', 'h-5', 'h-96',
        // Flex & Grid
        'flex', 'flex-1', 'flex-shrink-0', 'grid', 'grid-cols-2', 'items-start', 'items-center', 'items-end', 'justify-between', 'justify-start', 'justify-end',
        // Spacing
        'gap-2', 'gap-3', 'gap-4', 'gap-6', 'space-y-6',
        // Font
        'font-bold', 'font-semibold', 'font-medium', 'uppercase', 'tracking-wide',
        // Rounded
        'rounded-md', 'rounded-full', 'rounded-lg', 'overflow-hidden',
        // Transitions & Effects
        'transition', 'hover:bg-green-50', 'hover:border-green-500', 'hover:bg-blue-50', 'hover:bg-gray-100',
        // Shadow & Display
        'shadow', 'shadow-lg', 'sticky', 'top-0', 'overflow-y-auto',
        // Other
        'relative', 'absolute', 'inset-0', 'space-y-2',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
};
