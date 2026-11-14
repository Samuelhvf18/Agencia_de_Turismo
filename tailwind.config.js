import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', ...defaultTheme.fontFamily.serif],
            },

            colors: {
                pipocas: {
                    blue: '#1F5F8B',
                    dark: '#343A40',
                    light: '#457699',
                    terra: '#C96F4A',
                    gold: '#F4C95D',
                    background: '#F0F0F1',
                    muted: '#6B6F72',
                },
            },
        },
    },

    plugins: [forms, typography],
};