const Encore = require('@symfony/webpack-encore');
const GlobImporter = require('node-sass-glob-importer');
const NodePolyfillPlugin = require('node-polyfill-webpack-plugin');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('app', './assets/js/app.js')
    .addEntry('editor', './assets/js/editor.js')
    .addEntry('problems', './assets/js/problems.js')
    .addStyleEntry('simple', './assets/css/simple.scss')

    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    .enableSassLoader(function(options) {
        options.sassOptions.importer = GlobImporter();
    })
    .enablePostCssLoader()
    .enableVersioning(Encore.isProduction())

    .addPlugin(new NodePolyfillPlugin())

    /*
    .addPlugin(
        new CopyPlugin([
            {
                from: 'node_modules/chart.js/dist/Chart.min.js',
                to: './'
            }
        ])
    )*/
;

module.exports = Encore.getWebpackConfig();
