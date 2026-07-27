const mix = require('laravel-mix');
const webpack = require("webpack");
require("dotenv").config({ path: ".env" });

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

mix.js("resources/js/app.js", "public/js")
    .vue()
    .sass("resources/sass/app.scss", "public/css")
    .sourceMaps()
    .webpackConfig({
        plugins: [
            new webpack.DefinePlugin({
                "process.env": {
                    APP_URL: JSON.stringify(process.env.APP_URL),
                },
            }),
        ],
    });
