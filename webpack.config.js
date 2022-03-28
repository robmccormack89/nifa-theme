const TerserPlugin = require('terser-webpack-plugin');

var path = require('path');

module.exports = {
  
  optimization: {
    minimizer: [new TerserPlugin({
      extractComments: false,
    })],
  },
  
  // remove file size warnings from webpack, sets new limit
  performance: {
    hints: false,
    maxEntrypointSize: 512000,
    maxAssetSize: 512000
  },
    
  // sets mode to miniied production output, the entry file and the path & filename to output file
  mode: "production",
  entry: {
    base: './assets/js/srcs/index.js',
  },
  output: {
    filename: '[name].js',
    path: __dirname + '/assets/js',
  },
    
};