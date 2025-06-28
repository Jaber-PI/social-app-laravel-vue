import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
    optimizeDeps: {
        include: [
            "@ckeditor/ckeditor5-vue",
            "@ckeditor/ckeditor5-build-classic",
        ],
    },
    build: {
        commonjsOptions: {
            include: [/@ckeditor/, /node_modules/],
        },
    },
});
