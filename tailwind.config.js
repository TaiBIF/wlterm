const colors = require('tailwindcss/colors');

module.exports = {
    purge: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
    darkMode: false,
    theme: {
        extend: {
            flex: {
                half: '0 0 50%',
                triple: '0 0 33.3%',
                quater: '0 0 25%',
                penta: '0 0 20%',
                oct: '0 0 12.2%',
            },
        },
        colors: {
            black: '#373737',
            white: colors.white,
            gray: colors.gray,
            red: colors.rose,
            blue: {
                DEFAULT: '#8cc8f6',
            },
            jbgray: '#f7f9f8',
            jbdblue: '#2c91d4',
        },
    },
    variants: {
        extend: {
            fontWeight: ['hover'],
        },
    },
    plugins: [],
};
