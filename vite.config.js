import { defineConfig } from 'vite';
import path from 'path';
import liveReload from 'vite-plugin-live-reload'

export default defineConfig({
    base: './',
    build: {
        manifest: true,
        outDir: path.resolve(__dirname, 'dist'),
        sourcemap: false,
        emptyOutDir: true,
        copyPublicDir: true,
        commonjsOptions: { transformMixedEsModules: true },
        rollupOptions: {
            input: {
                app: path.resolve(__dirname, 'src/js/app.js'),
                admin: path.resolve(__dirname, 'src/js/admin.js'),
                accessibility_cloud: path.resolve(__dirname, 'src/js/accessibility_cloud.js'),
            },
            output: {
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && /\.(png|jpg|gif|svg|eot|ttf|woff|woff2)$/.test(assetInfo.name)) {
                        return 'media/[name][extname]';
                    }
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        return 'css/[name][extname]';
                    }
                    return 'assets/[name][extname]';
                },
            }
        }
    },
    css: {
        devSourcemap: true,
    },
    plugins: [
        liveReload('skeleton/**/*.twig'),
        liveReload('skeleton/**/*.yaml'),
    ],
    resolve: {
        extensions: ['.js'],
        alias: {
            '@public': ''
        },
    },
    server: {
        host: '0.0.0.0',
        origin: 'http://vite.internal:5174',
        port: 5174,
        strictPort: true,
        hmr: true,
        watch: {
            usePolling: true,
        }
    },
});
