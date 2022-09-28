const common = require("./webpack.common");
const { merge } = require("webpack-merge");
const path = require("path");
const TerserPlugin = require("terser-webpack-plugin");
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const webpack = require("webpack");

module.exports = merge(common, {
    output: {
        filename: "[name].js",
        path: path.resolve(__dirname, "../dist/prod"),
    },
    mode: "production",
    optimization: {
      minimizer: [new TerserPlugin({ extractComments: false })],
    },
    plugins: [
      new CleanWebpackPlugin({
        verbose: true,
      }),
      new webpack.ProvidePlugin({
        process: "process/browser",
      })
    ],
});
