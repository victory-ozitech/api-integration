import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/sass/app.scss", "resources/js/app.js"],
            refresh: true,
        }),
        vue(),
    ],

    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),

            "@components": path.resolve(__dirname, "resources/js/Components"),

            "@pages": path.resolve(__dirname, "resources/js/Pages"),

            "@Layouts": path.resolve(__dirname, "resources/js/Layouts"),

            "@data": path.resolve(__dirname, "resources/js/data"),

            "@sass": path.resolve(__dirname, "resources/sass"),
        },
    },

    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
