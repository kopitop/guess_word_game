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
    mix.scripts([
    		'chart.js/dist/Chart.js',
    		'wPaint/src/wPaint.js',
    	], 'public/js/vendor.js', 'node_modules')
	   .styles([
	   		'wPaint/src/wPaint.css'
	   	], 'public/css/vendor.css', 'node_modules')
	   .copy('node_modules/bootstrap-sass/assets/fonts', 'public/fonts')
	   .sass('app.scss')
       .webpack('app.js');
});
