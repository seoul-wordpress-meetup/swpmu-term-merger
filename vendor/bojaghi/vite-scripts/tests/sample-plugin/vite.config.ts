import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react-swc'

// https://vite.dev/config/
export default defineConfig({
  build: {
    manifest: true,
    modulePreload: {
      polyfill: true,
    },
    rollupOptions: {
      input: [
          'src/script-1.tsx',
          'src/script-2.tsx',
      ],
    }
  },
  publicDir: false,
  plugins: [react()],
})
