import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';
import path from 'path';

// Function to get theme CSS files
function getThemeCssFiles() {
  const themesDir = path.resolve(__dirname, 'resources/css/themes');
  if (!fs.existsSync(themesDir)) {
    return [];
  }

  return fs.readdirSync(themesDir)
    .filter(file => file.endsWith('.css'))
    .map(file => `resources/css/themes/${file}`);
}

export default defineConfig({
  plugins: [
    tailwindcss(),
    laravel({
      input: [
        'resources/css/app.css',
        'resources/css/webkernel.css',
        'resources/js/app.js',
        'resources/js/webkernel.js',
        // Dynamic theme CSS files will be automatically discovered
        ...getThemeCssFiles(),
      ],
      refresh: true,
    }),
  ],
  build: {
    minify: 'esbuild',
    assetsInlineLimit: 4096,
  },
  server: {
    hmr: false,
    watch: {
      usePolling: false,
      interval: 1000,
    },
  },
  css: {
    devSourcemap: false,
  },
});
