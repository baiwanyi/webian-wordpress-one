const path = require('path')
const webpack = require('webpack')

// 设定全局暴露
const extractJquery = new webpack.ProvidePlugin({
    $: 'jquery',
    jQuery: 'jquery',
    // _: 'lodash',
})

module.exports = {
    entry: {
        wwpo: './sources/wwpo.js',
    },
    output: {
        path: path.join(__dirname, './assets/js'),
        filename: '[name].min.js',
    },
    mode: 'production',
    // mode: 'development',
    // module: {
    //     rules: [
    //         {
    //             test: /.html$/,
    //             use: [
    //                 {
    //                     loader: 'html-loader'
    //                 }
    //             ]
    //         }
    //     ]
    // }
    plugins: [extractJquery]
}
