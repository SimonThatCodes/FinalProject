export default defineNuxtConfig({
  devtools: { enabled: true },
  compatibilityDate: '2025-12-09',
  css: ['~/assets/css/main.css'],
  runtimeConfig: {
    public: {
      apiBase: process.env.API_BASE_URL || 'http://localhost:8000/api',
    },
  },
  app: {
    head: {
      title: 'Adopet - Pet Adoption Platform',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      ],
    },
  },
})

