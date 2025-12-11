import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        https: process.env.APP_ENV === 'production', // Asumiendo que defines esto en tu .env
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
