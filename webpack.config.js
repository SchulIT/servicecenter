var Encore = require('@symfony/webpack-encore');
var CopyPlugin = require('copy-webpack-plugin');
const GlobImporter = require('node-sass-glob-importer');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    // .addEntry('js/app', './assets/js/app.js')
    // .addStyleEntry('css/app', './assets/css/app.scss')
    .addEntry('app', './assets/js/app.js')
    .addEntry('editor', './assets/js/editor.js')
    .addEntry('placards', './assets/js/placards.js')
    .addEntry('problems', './assets/js/problems.js')
    .addStyleEntry('simple', './vendor/schulit/common-bundle/Resources/assets/css/simple.scss')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader(function(options) {
        options.sassOptions.importer = GlobImporter();
    })
    .enablePostCssLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    // .autoProvidejQuery()

    .disableSingleRuntimeChunk()

    .addLoader(
        {
            test: /bootstrap\.native/,
            use: {
                loader: 'bootstrap.native-loader'
            }
        }
    )

    .addPlugin(
        new CopyPlugin([
            {
                from: 'vendor/emojione/emojione/assets/png',
                to: 'emoji/png'
            },
            {
                from: 'vendor/emojione/emojione/assets/svg',
                to: 'emoji/svg'
            },
            {
                from: 'node_modules/chart.js/dist/Chart.min.js',
                to: './'
            }
        ])
    )
;

module.exports = Encore.getWebpackConfig();
