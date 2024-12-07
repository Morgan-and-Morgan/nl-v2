const common = require("./webpack.common");
const { merge } = require("webpack-merge");
const path = require("path");
const ESLintPlugin = require("eslint-webpack-plugin");
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const webpack = require("webpack");

module.exports = merge(common, {
    output: {
        filename: "[name].js",
        path: path.resolve(__dirname, "../dist/dev"),
    },
    cache: true,
    mode: "development",
    optimization: {
        minimize: false,
    },
    plugins: [
      new ESLintPlugin(),
      new CleanWebpackPlugin(),
      new webpack.ProvidePlugin({
        process: "process/browser",
      })
    ],
});
