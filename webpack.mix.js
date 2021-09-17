const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

mix.js('resources/js/app.js', 'public/js')
    .vue({ version: 2 })
    .options({
        postCss: [tailwindcss('./tailwind.config.js')],
    })
    .sass('resources/sass/app.scss', 'public/css');
