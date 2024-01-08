const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require('path');

module.exports = {
  ...defaultConfig,
  entry: {
    'main': path.resolve(__dirname, 'src/main.js')
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'assets'),
    clean: true
  }
};