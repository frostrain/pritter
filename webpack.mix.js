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

// 复制整个文件夹, 注意 win 系统下的 watch 命令复制的 文件路径 存在问题,
// 不过 dev 命令没有问题
// mix.copy('resources/assets/loveliver/', 'public/loveliver/');

// 如果 copy 指定文件, 则 win 下的 watch 命令也可正常工作
// mix.copy('resources/assets/loveliver/css/app.css', 'public/loveliver/css/app.css');

// mix.sass('resources/assets/loveliver/css/app.scss', 'public/loveliver/css/app.css');
mix.less('resources/assets/loveliver/css/app.less', 'public/loveliver/css/app.css');
