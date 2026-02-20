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
            },
            colors:{
                //primaries
                'primarycolor' : '#F27D16',
                'primaryhovercolor' : '#F2A663',

                //secundaries
                'secondarycolor' : '#0D0D0D',
                'secondaryhcolor' : '#404040',

                //backgrounds
                'backgroundcolor' : '#F2F2F2',

                //others
                'dangercolor' : '#e02424',
                'dangerhcolor' : '#ff3333',

            }
        },
    },

    plugins: [forms, typography],
};
