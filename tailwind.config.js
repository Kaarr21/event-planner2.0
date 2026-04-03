import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Times New Roman', ...defaultTheme.fontFamily.serif],
                display: ['Times New Roman', ...defaultTheme.fontFamily.serif],
            },
            borderRadius: {
                'none': '0',
                'sm': '0.125rem',
                DEFAULT: '8px',
                'md': '0.375rem',
                'lg': '0.5rem',
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
                'full': '9999px',
                'lux': '8px',
            },
            colors: {
                brand: {
                    indigo: '#4338CA',
                    slate: '#F8FAFC',
                    pearl: '#FFFFFF',
                    accent: '#6366F1',
                    dark: '#0f172a',
                }
            },
            boxShadow: {
                'lux': '0 10px 30px rgba(0, 0, 0, 0.05)',
            }
        },
    },

    plugins: [forms, typography],
};

