module.exports = {
  mode: 'universal',
  /*
  ** Headers of the page
  */
  head: {
    title: 'Randa to Randa',
    meta: [
      {
        charset: 'utf-8',
      },
      {
        "http-equiv": "X-UA-Compatible",
        content: "IE=edge"
      },
      {
        name: 'theme-color',
        content: '#8d7a60'
      },
      {
        name: 'viewport',
        content: 'width=device-width, initial-scale=1'
      },
      {
        hid: 'og:site_name',
        property: 'og:site_name',
        content: 'Randa to Randa'
      },
      {
        hid: 'description',
        name: 'description',
        content: ''
      },
      {
        hid: 'og:description',
        property: 'og:description',
        content: ''
      },
      {
        hid: 'og:type',
        property: 'og:type',
        content: "website"
      },
      {
        hid: 'og:image',
        property: 'og:image',
        content: process.env.RANDA2RANDA_WEB_URL + "/ogimage.jpg"
      },
      {
        hid: 'twitter:image',
        property: 'twitter:image',
        content: process.env.RANDA2RANDA_WEB_URL + "/ogimage.jpg"
      },
      {
        hid: 'twitter:card',
        name: 'twitter:card',
        content: 'summary'
      },
      {
        hid: 'og:locale',
        property: 'og:locale',
        content: 'it_IT'
      },
    ],
    link: [{
      rel: 'icon',
      type: 'image/x-icon',
      href: '/favicon.ico'
    }]
  },
  /*
  ** Customize the progress-bar color
  */
  loading: '~/components/loading.vue',
  /*
  ** Global CSS
  */
  css: [
    '@/assets/scss/styles.scss'
  ],
  /*
  ** Plugins to load before mounting the App
  */
  plugins: [
    '~/plugins/bus',
    '~/plugins/global-components',
    '~/plugins/slug',
    '~/plugins/brand-info',
    '~/plugins/vuelidate',
    '~/plugins/vue-swal',
    '~/plugins/env',
    '~/plugins/jsonld',
    '~/plugins/recaptcha',
    {
      src: "~/plugins/iubenda",
      mode: 'client'
    },
    {
      src: "~/plugins/owl",
      ssr: false
    },
    {
      src: "~/plugins/gmap",
      ssr: true
    }
  ],
  /*
  ** Nuxt.js dev-modules
  */
  buildModules: [
  ],
  /*
  ** Nuxt.js modules
  */
  modules: [
    // Doc: https://bootstrap-vue.js.org
    'bootstrap-vue/nuxt',
    '@nuxtjs/axios',
    '@nuxtjs/style-resources',
    [
      'nuxt-mq',
      {
        breakpoints: {
          xs: 576,
          sm: 768,
          md: 992,
          lg: 1200,
          xl: Infinity
        },
        defaultBreakpoint: 'lg'
      }
    ],
    '@nuxtjs/google-analytics',
    [
      'nuxt-facebook-pixel-module',
      {
        track: 'PageView',
        pixelId: '479782379382353',
        disabled: true
      }
    ],
    ['nuxt-vuex-localstorage', {
      localStorage: [
        'shopping'
      ]
    }
    ]
  ],
  axios: {
    baseURL: process.env.WS_API_URL
  },
  styleResources: {
    scss: [
      '@/assets/scss/common.scss'
    ]
  },
  /*
  ** Build configuration
  */
  build: {
    /*
    ** You can extend webpack config here
    */
    transpile: [/^vue2-google-maps($|\/)/],
    extend(config, ctx) {
    }
  },
  bootstrapVue: {
    bootstrapCSS: false,
    bootstrapVueCSS: false
  },
  env: {
    apiUrl: process.env.WS_API_URL,
    webUrl: process.env.RANDA2RANDA_WEB_URL//,
    // gmapApiKey: process.env.GMAP_API_KEY,
    // iubendaSiteId: process.env.IUBENDA_SITE_ID,
    // iubendaCookiePolicyId: process.env.IUBENDA_COOKIE_POLICY_ID,
    // iubendaAcceptButtonColor: process.env.IUBENDA_ACCEPT_BUTTON_COLOR,
    // recaptchaKey: process.env.RECAPTCHA_KEY
  }//,
  // googleAnalytics: {
  //   id: "UA-7105931-34",
  //   dev: true,
  //   disabled: true
  // },
}
