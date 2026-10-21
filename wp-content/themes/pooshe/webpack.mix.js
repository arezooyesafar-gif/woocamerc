let mix = require('laravel-mix');

mix.js('dist/init.js', 'dist/js/app.js')
    .sass('dist/styles.scss', 'dist/style/main.css')
    .options({
    processCssUrls: false
});