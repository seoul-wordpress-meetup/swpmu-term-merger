import {defineConfig} from 'vite'
import react from '@vitejs/plugin-react-swc'
import path from 'path'

// https://vite.dev/config/
export default defineConfig({
    build: {
        emptyOutDir: true,
        manifest: true,
        modulePreload: {
            polyfill: true,
        },
        outDir: '../dist',
        rollupOptions: {
            input: [
                'src/term-merger.tsx',
            ],
        },
        sourcemap: true,
    },
    esbuild: {
        minifyIdentifiers: false,
        keepNames: true,
    },
    plugins: [react()],
    publicDir: false,
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './src'),
        },
    },
})
