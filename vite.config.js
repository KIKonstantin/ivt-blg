import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/styles/app.css",
                "resources/js/app.js",
                "resources/js/homepage.js",
                "resources/styles/pages/article.sass",
                "resources/js/scroll.js",
            ],
            refresh: true,
        }),
    ],
});
