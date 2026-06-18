import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [tailwindcss()],
    publicDir: false,
    build: {
        outDir: 'public',
        emptyOutDir: false,
        rollupOptions: {
            input: 'resources/js/portal-ui.js',
            output: {
                entryFileNames: 'portal-ui.js',
                assetFileNames: (assetInfo) => assetInfo.name && assetInfo.name.endsWith('.css')
                    ? 'portal-ui.css'
                    : '[name][extname]',
            },
        },
    },
});
