const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/**/*.html',
    ],

    theme: {
        extend: {
            fontFamily: {
                montserrat: ['Montserrat', 'sans-serif'],
                'main-font': 'Cormorant Garamond, serif',
                'infant_font': '"Cormorant Infant", serif',
                'montserrat_font': 'Montserrat, sans-serif',
                'roboto_font': 'Roboto, sans-serif',
                'roboto_condensed': 'Roboto Condensed, sans-serif',
                'new_york_font': '"New York", sans-serif',
                'geologica_font': '"Geologica", sans-serif',
                'inter_font': '"Inter", sans-serif',
                'snell_roundhand_font': '"SnellRoundhand"',
                'cormorant_sc_font': '"Cormorant SC"',
                cormorant: ['Cormorant', 'serif'],
            },
            padding: {
                '1.75': '0.4375rem',
                '4.5': '1.125rem'
            },
            borderRadius: {
                '4xl': '2rem'
            },
            fontSize: {
                '56': '3.5rem',
                '42': '2.625rem',
                '40': '2.5rem',
                '32': '2rem',
                '28': '1.75rem',
                '27': '27px',
                '26': '26px',
                '25': '25px',
                '24': '24px',
                '23': '23px',
                '22': '22px',
                '21': '21px',
                '20': '20px',
                '19': '19px',
                '18': '18px',
                '17': '17px',
                '16': '16px',
                '15': '15px',
                '14': '14px',
                '13': '13px',
                '12': '12px',
                '11': '11px',
                '10': '10px',
                '9': '9px',
                '8': '8px',
            },
            opacity: {
                '26': '0.26',
                '64': '0.64'
            },
            lineHeight: {
                '1.7': '1.7',
                '1.6': '1.58',
                '1.2': '1.2',
            },
            colors: {
                transparent: 'transparent',
                current: 'currentColor',
                lemousseColor: '#DBB0C1',
                myLightGray: '#F6F6F6',
                myDark: '#2C2E35',
                myCream: '#FBF7F2',
                myGray: '#B2B2B2',
                myGreen: '#D7DACD',
                myGreen2: '#6C715C',
                myGreen3: '#919583',
                myBrown: '#B1908E',
                myBeige: '#F4EEE7',
                myRed: '#AE1821',
                myCutomBrown: 'rgba(90,94,77,.64)',
                myCustomGreen: '#78796E',
                winterGreen: '#2C4B3A',
                cabinetGreen: '#BDC5B7',
                newYearSkBlack: '#242421',
                newYearSkConiferousGreen: '#242421',
                newYearSkDarkGreen: '#375545',
                myGreenLimitter: '#a5aa91',
                formGreen: '#6C715C',
                springGreen: '#9BACA1',
                taplinkPink: '#f0eae5',
                taplinkPink2: '#ded7d1',
                taplinkPink3: '#CBC2B8',
                taplinkWhite: '#fffcfa',
                taplinkGrey: '#e5e5e5',
                taplinkGrey2: '#CECECE',
                taplinkGreen: '#8EA49F',
                taplinkLimitterGreen: '#6ea48a',
                taplinkGreen2: '#d2dbd9',
                taplinkGreen3: '#cdd7d2',
                taplinkGreen4: '#acc2bd',
                taplinkGrey3: '#f1f0ee',
                taplinkGrey4: '#818180',
                happyBDRed: '#b3535f',
            },
            inset: {
                '18': '4.5rem',
                '-18': '-4.5rem',
            }
        },

    },

    plugins: [require('@tailwindcss/forms')],
};
