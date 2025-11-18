/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./components/**/*.{js,vue,ts}",
        "./layouts/**/*.vue",
        "./pages/**/*.vue",
        "./plugins/**/*.{js,ts}",
        "./app.vue",
        "./error.vue",
    ],
    theme: {
        extend: {
            spacing: {
                '20vh': '20vh', // Ajoute une taille personnalisée de 20vh
                '15vh': '15vh', // Ajoute une taille personnalisée de 15vh
                '10vh': '10vh', // Ajoute une taille personnalisée de 10vh
                '5vh': '5vh', // Ajoute une taille personnalisée de 5vh
            },
            fontSize: {
                'responsive-base': 'clamp(0.875rem, 1vw, 1rem)',
                'responsive-lg': 'clamp(1.2rem, 3vw, 2rem)',
            },
        },
    },
    plugins: [],
}

