const ExtractTextPlugin = require('extract-text-webpack-plugin');
const webpack = require('webpack');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const autoprefixer = require('autoprefixer');
//const $ = require('jquery');
//const jQuery = require ('jquery');
let path = require('path');

module.exports = {
    watch: true,
    entry: [
        './js/mainJS.js'
    ],
    output: {
        path: path.resolve(__dirname, './production/'),
        filename: 'build.js',
        publicPath: 'production/'
    },
    devtool: "source-map",
    devServer : { 
        overlay: true
    },
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                loader: 'babel-loader',
                exclude: /node_modules\/(?!(dom7|ssr-window|swiper)\/).*/,
            },
        ]
    },
    plugins: [
		new webpack.ProvidePlugin({
			$: 'jquery',
			'$': 'jquery',
			
			jquery: 'jquery',
			jQuery: 'jquery',
			'jQuery': 'jquery',
			'window.jquery': 'jquery',
			'window.jQuery': 'jquery',
			'window.$': 'jquery',
		}),
	]
};