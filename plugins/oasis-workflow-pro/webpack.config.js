const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');


module.exports = {
  mode: 'development',
  entry: {
    './dist/ow-gutenberg': './src/index.js',
  },
  output: {
    path: path.resolve(__dirname),
    filename: '[name].js',
  },
  // watch: true,
  devtool: 'production' === process.env.NODE_ENV ? false : 'source-map',
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          },
        },
      },
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          "css-loader",
          {
            loader: "postcss-loader",
            options: {
              postcssOptions: {
                plugins: [
                  [
                    "autoprefixer",
                  ],
                ],
              },
            },
          },
          {
            loader: "sass-loader",
            options: {
              sourceMap: true,
              sassOptions: {
                outputStyle: 'production' === process.env.NODE_ENV ? 'compressed' : 'expanded',
              },
            },
          },
        ],
      },
      {
        test: /\.(jpg|png)$/,
        use: ['url-loader']
      }
    ],
  },
  externals: {
    'react': 'React',
    'react-dom': 'ReactDOM',
  },
  plugins: [
    new MiniCssExtractPlugin({
      // Options similar to the same options in webpackOptions.output
      // all options are optional
      filename: './dist/ow-gutenberg.css',
      ignoreOrder: false, // Enable to remove warnings about conflicting order
    }),
    // new BrowserSyncPlugin({
    //   // Load localhost:3333 to view proxied site
    //   host: 'localhost',
    //   port: '3333',
    //   // Change proxy to your local WordPress URL
    //   proxy: 'https://gutenberg.local'
    // })
  ],
};
