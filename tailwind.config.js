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
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'premium': '0 10px 30px -10px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.05)',
                'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
            },
        },
    },

    plugins: [forms, require('daisyui')],
    daisyui: {
        themes: [
            {
                light: {
                    "primary": "#355872",
                    "secondary": "#7AAACE",
                    "accent": "#9CD5FF",
                    "neutral": "#355872",
                    "base-100": "#F7F8F0",
                    "base-200": "#EEEEE5",
                    "base-300": "#E5E5DA",
                    "--rounded-box": "1.2rem",
                    "--rounded-btn": "0.8rem",
                    "--rounded-badge": "1.9rem",
                },
            },
            {
                dark: {
                    "primary": "#7AAACE",
                    "secondary": "#355872",
                    "accent": "#9CD5FF",
                    "neutral": "#F7F8F0",
                    "base-100": "#1A2E3C",
                    "base-200": "#0D161D",
                    "base-300": "#091218",
                    "--rounded-box": "1.2rem",
                    "--rounded-btn": "0.8rem",
                    "--rounded-badge": "1.9rem",
                },
            },
        ],
    },
};
