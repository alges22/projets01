// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    devtools: {enabled: true},
    layout: 'default',
    css: [
        '~/assets/css/main.css',
        '@fortawesome/fontawesome-svg-core/styles.css',
        'awesome-notifications/dist/style.css'
    ],
    postcss: {
        plugins: {
            tailwindcss: {},
            autoprefixer: {},
        },
    },
    app: {
        head: {
            charset: 'utf-8',
            viewport: 'width=device-width, initial-scale=1',
        }
    },
    modules: [
        '@pinia/nuxt',
        '@sentry/nuxt/module'
    ],

    sentry: {
        dsn:"https://8ffc1b05cf7eb4f408cd9608fcc53361@o4504199028408320.ingest.us.sentry.io/4507737198952448",
    },

    runtimeConfig: {
        public: {
            CLIENT_ID: '',
            CLIENT_SECRET: ''
        },
    }
})
