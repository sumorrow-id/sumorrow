/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'morning-mist': '#BDD8E9',
        'sky-oxygen': '#7CBCE8',
        'lithic-blue': '#6EA1B2',
        'glacial-teal': '#4D8EA2',
        'blue-bird': '#49769F',
        'summit-blue': '#094174',
        'deep-midnight': '#001E3A',
        primary: '#094174',
        heading: '#001E3A',
        'blue-support': '#7CBCE8',
        surface: '#BDD8E9'
      },
      fontFamily: {
        'sans': ['"Plus Jakarta Sans"', 'sans-serif'],
        'display': ['"Bricolage Grotesque"', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
