/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './src/**/*.js',
        './src/app/**/*.latte',
        '!./src/vendor',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}