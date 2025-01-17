/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './src/**/*.js',
        './src/app/**/*.latte',
        '!./src/vendor',
    ],
	safelist: [
	  'hover:text-pink-600',
	  'w-24'
	],
    theme: {
        extend: {},
    },
    plugins: []
}