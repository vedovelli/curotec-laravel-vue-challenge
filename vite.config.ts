import { defineConfig } from 'vitest/config';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { resolve } from 'node:path';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import vueDevTools from 'vite-plugin-vue-devtools';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.ts'],
      ssr: 'resources/js/ssr.ts',
      refresh: true,
    }),
    tailwindcss(),
    vueDevTools(),
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
      '@': path.resolve(__dirname, './resources/js'),
      'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
    },
  },
  test: {
    globals: true,
    environment: 'happy-dom',
    include: ['resources/js/**/*.{test,spec}.{js,ts,vue}'],
    setupFiles: ['./tests/frontend/setup.ts'],
  },
});
