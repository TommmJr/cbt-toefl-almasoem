import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/timer-ujian.js',
                'resources/js/kunci-tab.js',
                'resources/js/audio-player.js',
            ],
            refresh: true,
        }),
    ],
    // Konfigurasi untuk cross-platform compatibility
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: false,
        hmr: {
            host: 'localhost',
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});