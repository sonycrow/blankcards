module.exports = {
    content: [
        "src/{css,html,js}/*.{html,twig,js,css}"
    ],
    darkMode: false,
    theme: {
        extend: {
            gridTemplateRows: {
                // Simple 8 row grid
                '8': 'repeat(8, minmax(0, 1fr))',
            }
        }
    },
    plugins: [
        require("@tailwindcss/forms")
    ],
}