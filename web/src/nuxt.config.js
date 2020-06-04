import colors from 'vuetify/es5/util/colors'

export default {
  mode: 'spa',
  /*
  ** Headers of the page
  */
  head: {
    titleTemplate: '%s - ' + process.env.npm_package_name,
    title: process.env.npm_package_name || '',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: process.env.npm_package_description || '' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
      { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Muli:wght@200;400&display=swap'}
    ]
  },
  /*
  ** Customize the progress-bar color
  */
  loading: { color: '#fff' },
  /*
  ** Global CSS
  */
  css: [
  ],
  /*
  ** Plugins to load before mounting the App
  */
  plugins: [
    { src: '~/plugins/vuex-persist', ssr: false }
  ],
  /*
  ** Nuxt.js dev-modules
  */
  buildModules: [
    '@nuxtjs/vuetify',
  ],
  /*
  ** Nuxt.js modules
  */
  modules: [
    '@nuxtjs/axios',
    '@nuxtjs/auth'
  ],
  css: [
    '@/assets/styles/style.scss'
  ],
  /*
  ** vuetify module configuration
  ** https://github.com/nuxt-community/vuetify-module
  */
  vuetify: {
    customVariables: ['~/assets/variables.scss'],
    theme: {
      dark: false,
      themes: {
        light: {
          primary:"#cf2030",
          accent: "#CFCA34",
          secondary: "#4F0C11",
          info: colors.teal.lighten1,
          warning: colors.amber.base,
          error: colors.deepOrange.accent4,
          success: colors.green.accent3
        }
      }
    }
  },
  /*
  ** Build configuration
  */
  build: {
    /*
    ** You can extend webpack config here
    */
    extend (config, ctx) {
    }
  },
  env: {
    base_url: "http://api.randa2randa.test",
    client_id: "Randa2RandaAppClient",
    client_secret: "FwPMFRlCa78GPQrO9zRWVRbjPCoPmaBQP254nx3g"
  },
  auth: {
    redirect: {
      logout: '/login',
      login: '/login',
    },
    rewriteRedirects: false,
    strategies: {
      local: {
        endpoints: {
          login: { url: 'http://api.randa2randa.test/token', method: 'post', propertyName: 'access_token' },
          logout: { url: 'http://api.randa2randa.test/revoke', method: 'post'},
          user: { url: 'http://api.randa2randa.test/me', method: 'get', propertyName: false}
          //todo logout
        }
      }
    }
  }
}
