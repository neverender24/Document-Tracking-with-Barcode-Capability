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

mix.js([
    'resources/assets/js/app.js',
    'node_modules/vuelidate/dist/vuelidate.min.js',
    ], 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
     .extract(['jquery', 'bootstrap', 'vue', 'lodash', 'element-ui', 'vue-barcode', 'vue-search-select'])
     .version()
     .sourceMaps()
     .webpackConfig({
      resolve: {
          alias: {
              'vue$': 'vue/dist/vue.esm.js', // Use Vue's runtime + compiler build
          },
      },
  });
