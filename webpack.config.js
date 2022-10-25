const path = require("path");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
    plugins: [
        new HtmlWebpackPlugin({
            template: './src/index.html'
        }),
        new MiniCssExtractPlugin({
            filename: "[name].css"
        })
    ],
    entry: "./src/index.js",
    output: {
        filename: "app.js",
        path: path.resolve(__dirname, "app/dist"),
        publicPath: "./dist/",
        assetModuleFilename: 'assets/resources/[name][ext][query]'
    },
    module: {
        rules: [
            {
                test: /\.(c|sc|sa)ss$/,
                use: [
                    "style-loader",
                    {
                        loader: "css-loader",
                        options: {
                            importLoaders: 1
                        }
                    },
                    "postcss-loader"
                ],
            }
        ]
    }
}