const config = require("../buildconfig");

module.exports = {
    entry: config.webpack.entryPoints,
    resolve: {
        extensions: [".js", ".jsx"],
    },
    module: {
        rules: [
            {
                test: /\.(js|jsx)?$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["@babel/preset-react"],
                    },
                },
            }
        ],
    },
    externals: {
        jquery: "jQuery",
    },
};
