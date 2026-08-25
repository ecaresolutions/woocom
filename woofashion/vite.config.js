import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';
export default defineConfig({
    plugins: [
        tailwindcss(),
        react()
    ],
    base: '',
    build: {
        manifest: true,
        outDir: 'dist',
        rollupOptions: {
            input: 'src/main.tsx',
        },
    },
    server: {
        cors: true,
        strictPort: true,
        port: 5173,
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './src'),
        },
    },
});
