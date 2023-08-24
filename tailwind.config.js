/** @type {import('tailwindcss').Config} */
module.exports = {

  daisyui: {
    themes: ["light", "dark", "cupcake", "winter"],
  },
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./node_modules/flowbite/**/*.js"
  ],

  plugins: [
      require("daisyui", "@tailwindcss/forms"),
      require('flowbite/plugin')

  ],
}

