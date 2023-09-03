const path = require('path')

module.exports = {
    entry: {
        apps: './plugins/webian-wordpress-one/apps.js',
    },
    output: {
        filename: '[name].min.js',
    },
    // mode: 'production',
    mode: 'development',
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
