const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

mix.js('resources/js/app.js', 'public/js')
    .vue({ version: 2 })
    .sass('resources/sass/app.scss', 'public/css')
    .options({
        postCss: [tailwindcss('./tailwind.config.js')],
    })
    .webpackConfig({
        output: {
            chunkFilename: mix.inProduction() ? 'js/chunks/[name].[chunkhash].js' : 'js/chunks/[name].js',
        },
    })
    .version();
