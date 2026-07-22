import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import symfonyPlugin from 'vite-plugin-symfony';

export default defineConfig({
    plugins: [vue(), symfonyPlugin()],

    resolve: {
        alias: {
            '@': path.resolve(__dirname, './assets/vue'),
        },
    },

    build: {
        outDir: 'public/build',
        emptyOutDir: true,

        rollupOptions: {
            input: {
                app: './assets/vue/main.js'
            },
        },
    },

    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        origin: 'http://typer.ddev.site:5173',

        cors: true,

        headers: {
            'Access-Control-Allow-Origin': '*',
        },

        allowedHosts: [
            'typer.ddev.site',
        ],

        hmr: {
            host: 'typer.ddev.site',
            protocol: 'ws',
            clientPort: 5173,
        },
    },
})
