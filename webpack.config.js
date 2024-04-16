const path = require('path')

module.exports = {
    entry: {
        wwpo: './sources/index.js',
    },
    output: {
        path: path.join(__dirname, './assets/js'),
        filename: '[name].min.js',
    },
    // mode: 'development',
    mode: 'production',
    externals: {
        jquery: 'jQuery',
        underscore: '_'
    },
    module: {
        rules: [
            {
                test: /.html$/,
                use: [
                    {
                        loader: 'html-loader'
                    }
                ]
            }
        ]
    }
}
