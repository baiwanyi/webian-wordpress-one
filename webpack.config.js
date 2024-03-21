const path = require('path')

module.exports = {
    entry: {
        apps: './wp.webian.dev/wp-content/plugins/webian-wordpress-one/wwpo.js',
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
