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
            colors: {
                cream: '#FFF4E6',
                dough: '#E7D3B0',
                caramel: '#C68B59',
                cocoa: '#3B2A1A',
                butter: '#FFD56A',
                strawberry: '#FF6B6B',
                softblack: '#1C1C1C',
                lightgray: '#F5F5F5',
            },
            fontFamily: {
                serif: ['"Playfair Display"', 'serif'],
                sans: ['Inter', 'sans-serif'],
                mono: ['"Space Grotesk"', 'sans-serif'],
            },
            boxShadow: {
                soft: '0 12px 32px rgba(59, 42, 26, 0.05)',
                float: '0 24px 48px rgba(59, 42, 26, 0.08)',
            },
            borderRadius: {
                '4xl': '24px',
            },
        },
    },

    plugins: [forms],
};
