<template>
  <v-app>
    <nav v-if="getToken() && getRegionName()">
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
                <v-icon>mdi-user-arrow-right-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Account</v-list-item-title>
            </v-list-item>

            <v-list-item @click="doLogout()">
              <v-list-item-icon>
                <v-icon>mdi-exit</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Logout</v-list-item-title>
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
          <div>
            {{ getUser() }} <small>({{ getRegionName() }})</small>
          </div>
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
      drawer: false,
      region: this.$store.getters["getRegion"]
    };
  },
  methods: {
    getUser() {
      return this.$store.getters["getUser"].fullName;
    },
    changeRegion() {
      this.$store.commit("setRegion", null);
      this.$router.push({
        path: "/login"
      });
    },
    getRegionName() {
      return this.$store.getters["getRegion"]
        ? this.$store.getters["getRegion"].name
        : null;
    },
    getToken() {
      return this.$store.getters["getToken"];
    },
    async doLogout() {
      try {
        await ApiServer.logout();
        this.$store.commit("setToken", null);
        this.$store.commit("setRegion", null);
        this.$router.push("/login");
      } catch (e) {

      }
    }
  },

  mounted() {
    window.onNuxtReady(() => {
      let token = this.getToken();
      if (token) {
        ApiServer.setToken(token);
        debugger;
        if (!this.$store.getters["getRegion"]) {
          this.$router.push("login");
        }
        ApiServer.base_url = process.env.base_url + "/";

        this.$store.watch(
          state => {
            return this.$store.state.region;
          },
          (newValue, oldValue) => {
            this.region = newValue;
          }
        );
      } else {
        this.$router.push({
          path: "/login"
        });
      }
    });
  }
};
</script>
