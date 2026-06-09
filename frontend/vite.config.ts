import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    vue(),
    { name: 'favicon-ico', configureServer(s) { s.middlewares.use((req, res, next) => { if (req.url === '/favicon.ico') req.url = '/favicon.svg'; next() }) } },
  ],
  server: {
    host: '0.0.0.0',
    port: 3000,
    proxy: {
      '/api': {
        target: process.env.VITE_API_TARGET || 'http://localhost:8000',
        changeOrigin: true,
      },
      '/images': {
        target: process.env.VITE_API_TARGET || 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
  optimizeDeps: { include: ['vue', 'vue-router', 'axios', 'pinia'] },
})
