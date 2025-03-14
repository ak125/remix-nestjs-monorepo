import { resolve } from 'path';
import { vitePlugin as remix } from '@remix-run/dev';
import { installGlobals } from '@remix-run/node';
import { flatRoutes } from 'remix-flat-routes';
import { defineConfig } from 'vite';
import tsconfigPaths from 'vite-tsconfig-paths';
import react from '@vitejs/plugin-react';
import { OrdersService } from '../orders.service';

const MODE = process.env.NODE_ENV;
installGlobals();

export default defineConfig({
  resolve: {
    preserveSymlinks: true,
    alias: {
      '~': '/src',
    },
  },

  build: {
    cssMinify: MODE === 'production',
    sourcemap: true,
    outDir: 'dist',
    commonjsOptions: {
      include: [/frontend/, /backend/, /node_modules/],
    },
    rollupOptions: {
      external: [/^remix-flat-routes.*/],
    },
  },

  server: {
    port: 3000,
    watch: {
      include: ['src/**/*'],
    },
  },

  plugins: [
    react(),
    tsconfigPaths(),
    remix({
      ignoredRouteFiles: ['**/*'],
      future: {
        v3_fetcherPersist: true,
      },
      serverModuleFormat: 'esm',
      routes: async (defineRoutes) => {
        return flatRoutes('routes', defineRoutes, {
          ignoredRouteFiles: [
            '.*',
            '**/*.css',
            '**/*.test.{js,jsx,ts,tsx}',
            '**/__*.*',
          ],
          appDir: resolve(__dirname, 'app'),
        });
      },
    }),
  ],
});