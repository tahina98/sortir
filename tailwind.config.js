/** @type {import('tailwindcss').Config} */
module.exports = {

  daisyui: {
    themes: ["light", "dark", "cupcake", "winter"],
  },
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],

  plugins: [ require("daisyui")],
}

