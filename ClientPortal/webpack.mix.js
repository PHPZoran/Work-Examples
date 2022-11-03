let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js(['resources/assets/js/app.js',
        'resources/assets/js/bootstrap.js'], 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .styles([
       'resources/assets/css/custom.css',
       'node_modules/datatables/media/css/jquery.dataTables.min.css',
       'resources/assets/css/jquery.multiselect.css'
   ], 'public/css/custom.css')
    .js(['resources/assets/js/reports.js',
         'resources/assets/js/accounts.js'], 'public/js/custom.js');
