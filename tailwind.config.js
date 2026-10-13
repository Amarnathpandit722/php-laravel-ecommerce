// tailwind.config.js
export default {
    darkMode: "class", // ← MUST be here, not 'media'
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: { extend: {} },
    plugins: [],
};
