<template>
  <v-app>
    <nav v-if="$auth.loggedIn && getRegion()">
      <v-navigation-drawer v-model="drawer" absolute temporary right>
        <v-list nav dense>
          <v-list-item-group active-class="secondary--text text--accent-4">
            <v-list-item to="/home">
              <v-list-item-icon>
                <v-icon>mdi-home</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Home</v-list-item-title>
            </v-list-item>

            <v-list-item to="/login">
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Account</v-list-item-title>
            </v-list-item>

            <v-list-item @click="changeRegion()">
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Cambia region</v-list-item-title>
            </v-list-item>

          </v-list-item-group>
        </v-list>
      </v-navigation-drawer>
      <v-toolbar color="primary" class="white--text">
        <v-btn class="white--text" text link to="/home">
          Home
        </v-btn>
        <v-btn class="white--text" text link to="/chapters">
          Capitoli
        </v-btn>
        <v-btn class="white--text" text to="/pippo">
          Randa
          <v-icon>mdi-menu-down</v-icon>
        </v-btn>
        <v-btn class="white--text" text to="/pippo">
          Directors
          <v-icon>mdi-menu-down</v-icon>
        </v-btn>
        <v-btn class="white--text" text to="/pippo">
          Economics
        </v-btn>
        <v-spacer></v-spacer>
        <v-toolbar-title class="d-flex flex-row align-center">
          <div>{{ $auth.user.fullName }} <small>({{getRegion()}})</small></div>
          <v-btn icon @click="drawer = true" class="white--text">
            <v-icon>mdi-account</v-icon></v-btn
          ></v-toolbar-title
        >
      </v-toolbar>
    </nav>
    <nuxt />
  </v-app>
</template>

<script>
import ApiServer from "../services/ApiServer";

export default {
  data() {
    return {
      drawer: false
    };
  },
  methods: {
    getRegion() {
      return localStorage.getItem("region");
    },
    changeRegion() {
      localStorage.removeItem("region");
      this.$router.push({
        path: "/login"
      });
    }
  },
  created() {
    
    if(this.$auth.loggedIn) {
      ApiServer.setToken(localStorage.getItem("auth._token.local"))
    }
    ApiServer.base_url = process.env.base_url + "/";
  }
};
</script>
