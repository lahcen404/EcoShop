import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    host: true, // Listen on all local IPs
    port: 5173,
    watch: {
      usePolling: true, // Enable for Docker Windows/Mac volume mounts
    }
  }
})
