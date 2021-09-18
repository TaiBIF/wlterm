module.exports = {
    purge: ['./resources/**/*.{vue,js,ts,jsx,tsx}'],
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
    },
    plugins: [],
};
