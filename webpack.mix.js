const { mix } = require('laravel-mix');

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

// mix.js('resources/assets/js/app.js', 'public/loveliver/js')
//    .sass('resources/assets/sass/app.scss', 'public/loveliver/css');


// watch 模式下 copy 可以自动复制更新过的文件
mix.copy('resources/assets/loveliver', 'public/loveliver');
