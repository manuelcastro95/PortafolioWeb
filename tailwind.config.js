import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                'nunito': ['Nunito', 'sans-serif'],
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#E74C3C',    // Rojo del logo
                secondary: '#2C3E50',  // Azul oscuro para contraste
                accent: '#3498DB',     // Azul para acentos
                warm: '#ECF0F1',       // Gris cálido para fondos
                soft: '#F8F9FA',       // Blanco suave
                dark: '#1A1A1A',       // Negro suave
                'light-gray': '#F5F5F5' // Gris claro
            },
            boxShadow: {
                'custom': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                '2xl': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                '3xl': '0 35px 60px -15px rgba(0, 0, 0, 0.3)',
            },
            animation: {
                'gradient': 'gradient 8s ease infinite',
                'fadeIn': 'fadeIn 0.6s ease-out forwards',
                'slideIn': 'slideIn 0.5s ease-out forwards',
                'float': 'float 3s ease-in-out infinite',
            },
            keyframes: {
                gradient: {
                    '0%, 100%': {
                        'background-position': '0% 50%'
                    },
                    '50%': {
                        'background-position': '100% 50%'
                    },
                },
                fadeIn: {
                    'from': { opacity: '0', transform: 'translateY(20px)' },
                    'to': { opacity: '1', transform: 'translateY(0)' },
                },
                slideIn: {
                    'from': { transform: 'translateX(-100%)' },
                    'to': { transform: 'translateX(0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
            }
        },
        container: {
            center: true,
            padding: '1rem'
        },
    },

    plugins: [forms],
};
