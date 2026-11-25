import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/mallardduck/blade-lucide-icons/resources/views/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['IBM Plex Sans Arabic', 'Manrope', ...defaultTheme.fontFamily.sans],
                display: ['Manrope', 'IBM Plex Sans Arabic', ...defaultTheme.fontFamily.sans],
                arabic: ['IBM Plex Sans Arabic', ...defaultTheme.fontFamily.sans],
            },
            maxWidth: {
                content: '75rem', // 1200px
                reading: '47.5rem', // 760px
            },
            colors: {
                canvas: {
                    DEFAULT: '#F8F8F5',
                    soft: '#F1F1EC',
                    subtle: '#F5F5F1',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    subtle: '#F5F5F1',
                },
                primary: {
                    DEFAULT: '#777A91',
                    dark: '#55596F',
                    soft: '#E9EAF0',
                },
                sage: {
                    DEFAULT: '#9AAE9D',
                    soft: '#EAF0E9',
                    dark: '#6F947A',
                },
                sand: '#D8CBB8',
                beige: '#F1EBE2',
                ink: {
                    DEFAULT: '#30332F',
                    secondary: '#73766F',
                    muted: '#969990',
                },
                line: {
                    DEFAULT: '#E8E9E3',
                    strong: '#DADCD4',
                },
                success: '#6F947A',
                warning: '#B58F57',
                danger: '#B97878',
                info: '#7C91A7',
                // Compatibility aliases used across existing views
                ivory: '#F8F8F5',
                charcoal: '#30332F',
                terracotta: '#B97878',
                mint: '#EAF0E9',
                peach: '#F1EBE2',
                sun: '#F1EBE2',
                sky: '#E9EAF0',
                lavender: '#E9EAF0',
            },
            borderRadius: {
                control: '0.625rem', // 10px
                card: '0.875rem', // 14px
                section: '1.125rem', // 18px
            },
            boxShadow: {
                soft: '0 1px 0 rgba(48, 51, 47, 0.03)',
            },
            fontSize: {
                display: ['2.75rem', { lineHeight: '1.25', fontWeight: '600' }],
                h1: ['2.125rem', { lineHeight: '1.3', fontWeight: '600' }],
                h2: ['1.75rem', { lineHeight: '1.35', fontWeight: '600' }],
                h3: ['1.375rem', { lineHeight: '1.4', fontWeight: '600' }],
                h4: ['1.125rem', { lineHeight: '1.4', fontWeight: '600' }],
            },
        },
    },
    plugins: [forms],
};
