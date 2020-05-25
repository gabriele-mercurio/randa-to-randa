import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import VueRouter from 'vue-router'
import Chapters from './pages/Chapters'
import axios from 'axios'


Vue.config.productionTip = false
Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    component: Chapters
  },
  {
    name: 'Chapters',
    path: "/chapters",
    component: Chapters
  },
  {
    name: "Login",
    path: "/login",
    component: Chapters
  },
  {
    name: "Menu",
    path: "/menu",
    component: Chapters
  },

];

const router = new VueRouter({
  'routes': routes
});

router.beforeResolve((to, from, next) => {
  //TODO
  let logged = true;
  if (!logged && to.name !== "Login") {
    next({ name: 'Login' })
  } else {
    next();
  }
});


//TODO
let access_token = null;
if (access_token) {
  axios.defaults.headers.common['Authorization'] = "Bearer " + access_token;
}
axios.defaults.withCredentials = true;


new Vue({
  vuetify,
  router,
  render: h => h(App),
}).$mount('#app')
