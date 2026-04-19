import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: "#2F6B4F",
                    dark: "#25563F",
                    light: "#E6F2EC",
                },
                bgsoft: "#F5F7F6",
                textmain: "#1F2937",
                textsub: "#6B7280",
                bordersoft: "#E5E7EB",
                secondary: "#2F6B4F",
            },
        },
    },

    plugins: [forms],
};
