const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix.sass('app.scss')
        .copy(
            'resources/assets/images/*',
            'public/images/'
        )
        .scripts([
            'wPaint/lib/jquery.ui.core.1.10.3.min.js',
            'wPaint/lib/jquery.ui.widget.1.10.3.min.js',
            'wPaint/lib/jquery.ui.mouse.1.10.3.min.js',
            'wPaint/lib/jquery.ui.draggable.1.10.3.min.js',
            'wPaint/lib/wColorPicker.min.js',
            'wPaint/wPaint.min.js',
            'wPaint/plugins/main/wPaint.menu.main.min.js',
            'wPaint/plugins/text/wPaint.menu.text.min.js',
            'wPaint/plugins/shapes/wPaint.menu.main.shapes.min.js',
            'wPaint/plugins/file/wPaint.menu.main.file.min.js',
            'chart.js/dist/Chart.js',
            './resources/assets/js/laroute.js'
       	], 'public/js/vendor.js', './node_modules/')
        .styles([
            'wPaint/lib/wColorPicker.min.css',
            'wPaint/wPaint.min.css',
        ], 'public/css/vendor.css', 'node_modules')
        .copy('node_modules/bootstrap-sass/assets/fonts', 'public/fonts')
        .copy([
            'node_modules/wPaint/plugins/',
        ], 'public/plugins')
        .sass('error.scss')
        .webpack('app.js');
});
