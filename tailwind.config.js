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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                // Tipografía de Pipocas (igual que tours/show.blade.php)
                titulo: ['Fraunces', 'Georgia', ...defaultTheme.fontFamily.serif],
                cuerpo: ['"Plus Jakarta Sans"', 'system-ui', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                pipocas: {
                    principal: '#1F5F8B',
                    secundario: '#C96F4A',
                    acento: '#F4C95D',
                    complementario: '#457699',
                    fondo: '#F0F0F1',
                    texto: '#343A40',
                    'texto-2': '#6B6F72',
                    calido: '#6B4F3A',
                    hover: '#174870',
                },
            },
            boxShadow: {
                tarjeta: '0 20px 40px -30px rgba(18, 58, 90, .35)',
            },
        },
    },

    plugins: [forms, typography],
};
