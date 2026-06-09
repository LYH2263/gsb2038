/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        tea: {
          50: '#f0fdf4',
          100: '#dcfce7',
          700: '#15803d',
          800: '#166534',
        },
      },
    },
  },
  plugins: [],
}
