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
                    "primary": "#6366f1",
                    "secondary": "#a855f7",
                    "accent": "#2dd4bf",
                    "neutral": "#1e1b4b",
                    "base-100": "#ffffff",
                    "base-200": "#f8fafc",
                    "base-300": "#f1f5f9",
                    "--rounded-box": "1.2rem",
                    "--rounded-btn": "0.8rem",
                    "--rounded-badge": "1.9rem",
                },
                dark: {
                    "primary": "#818cf8",
                    "secondary": "#c084fc",
                    "accent": "#2dd4bf",
                    "neutral": "#f8fafc",
                    "base-100": "#0f172a",
                    "base-200": "#1e293b",
                    "base-300": "#334155",
                    "--rounded-box": "1.2rem",
                    "--rounded-btn": "0.8rem",
                    "--rounded-badge": "1.9rem",
                },
            },
        ],
    },
};
