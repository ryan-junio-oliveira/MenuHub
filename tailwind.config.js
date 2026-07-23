import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        { pattern: /^bg-(blue|amber|purple|green)-(100|500)$/ },
        { pattern: /^text-(blue|amber|purple|green)-700$/ },
        { pattern: /^border-l-(blue|amber|purple|green)-500$/ },
        'bg-blue-900/30', 'bg-amber-900/30', 'bg-purple-900/30', 'bg-green-900/30',
        'text-blue-300', 'text-amber-300', 'text-purple-300', 'text-green-300',
        'ring-red-400',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                '2xs': ['0.625rem', { lineHeight: '0.875rem' }],
                '3xl': ['1.875rem', { lineHeight: '2.25rem', letterSpacing: '-0.02em' }],
                '4xl': ['2.25rem', { lineHeight: '2.75rem', letterSpacing: '-0.03em' }],
            },
            colors: {
                primary: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#f97316',
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                    950: '#431407',
                },
                surface: {
                    DEFAULT: '#f8fafc',
                    dark: '#0f172a',
                },
                card: {
                    DEFAULT: '#ffffff',
                    dark: '#1e293b',
                },
                border: {
                    DEFAULT: '#e2e8f0',
                    dark: '#334155',
                },
                text: {
                    primary: '#0f172a',
                    secondary: '#64748b',
                    dark: '#f8fafc',
                },
            },
            borderRadius: {
                xl: '0.75rem',
                '2xl': '1rem',
                '3xl': '1.25rem',
            },
            boxShadow: {
                card: '0 1px 3px 0 rgb(0 0 0 / 0.04), 0 1px 2px -1px rgb(0 0 0 / 0.06)',
                'card-hover': '0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05)',
                'card-lg': '0 10px 15px -3px rgb(0 0 0 / 0.04), 0 4px 6px -4px rgb(0 0 0 / 0.04)',
                'dropdown': '0 4px 16px -2px rgb(0 0 0 / 0.08), 0 2px 4px -2px rgb(0 0 0 / 0.04)',
            },

            height: {
                navbar: '72px',
            },
            transitionTimingFunction: {
                'bounce-in': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
            },
        },
    },

    plugins: [forms],
};
