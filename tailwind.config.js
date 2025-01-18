/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './src/**/*.{js,ts,jsx,tsx}',
        './src/app/**/*.latte',
        '!./src/vendor',
    ],
	safelist: [
	  'hover:text-pink-600',
	  'w-24',
	  'ml-2 ml-4 ml-8 ml-12 ml-16 ml-20'
	],
    theme: {
        extend: {},
    },
    plugins: []
}