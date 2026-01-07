/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html', // main entry HTML
    './src/**/*.{vue,js,ts,jsx,tsx}', // all Vue + JS/TS components
  ],
  theme: {
    extend: {
      width: {
        screen: '100vw',
      },
      height: {
        screen: '100vh',
      },
    },
  },
  plugins: [],
}
