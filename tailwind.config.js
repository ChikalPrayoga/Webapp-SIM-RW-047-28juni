import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    primary: '#004D40',       // rgb(0, 77, 64)
                    secondary: '#A5D6A7',     // rgb(165, 214, 167)
                    dark: '#37474F',          // rgb(55, 71, 79)
                }
            }
        },
    },

    plugins: [forms],
};

