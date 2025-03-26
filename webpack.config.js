const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  entry: {
    main: './src/assets/js/index.js',       // Vstup pro hlavní CSS + JS
    admin: './src/assets/js/admin.js'       // Vstup pro admin CSS + JS
  },
  output: {
    path: path.resolve(__dirname, 'src/www/assets/js/'),
    filename: '[name].bundle.js' // Výstupní JS soubory: main.bundle.js, admin.bundle.js
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,  // Extrahuje CSS do souboru
          'css-loader',                 // Převede CSS do JS
          'sass-loader'                 // Převede SCSS na CSS
        ]
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({ filename: '../css/[name].css' }) // Výstupní CSS: main.css, admin.css
  ],
  mode: 'development',
  devtool: 'source-map' // Povolení source maps
};