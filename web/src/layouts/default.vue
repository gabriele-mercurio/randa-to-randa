<template>
  <v-app>
    <nav v-if="getToken() && getRegion()">
      <v-navigation-drawer v-model="drawer" absolute temporary right>
        <v-list nav dense>
          <v-list-item-group active-class="secondary--text text--accent-4">
            <v-list-item to="/home">
              <v-list-item-icon>
                <v-icon>mdi-home</v-icon>
              </v-list-item-icon>
              <v-list-item-title>{{ $t("home") }}</v-list-item-title>
            </v-list-item>

            <v-list-item to="/login">
              <v-list-item-icon>
                <v-icon>mdi-user-arrow-right-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-title>{{ $t("account") }}</v-list-item-title>
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
              <v-list-item-title>{{ $t("change_region") }}</v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-navigation-drawer>
      <v-toolbar color="primary" class="white--text">
        <v-btn class="white--text" text link to="/home">
          {{ $t("home") }}
        </v-btn>
        <v-btn class="white--text" text link to="/chapters">
          {{ $t("chapters") }}
        </v-btn>
        <v-btn class="white--text" text to="/randa">
          {{ $t("randa") }}
          <v-icon>mdi-menu-down</v-icon>
        </v-btn>
        <v-menu offset-y>
          <template v-slot:activator="{ on }">
            <v-btn class="white--text" text v-on="on">
              {{ $t("directors") }}
              <v-icon>mdi-menu-down</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item>
              <v-btn text to="/directors">
                {{ $t("management") }}
              </v-btn>
            </v-list-item>
            <v-list-item>
              Compensi
            </v-list-item>
          </v-list>
        </v-menu>

        <v-btn class="white--text" text to="/economics">
          Economics
        </v-btn>
        <v-spacer></v-spacer>
        <v-toolbar-title class="d-flex flex-row align-center">
          <div>
            {{ getUser() }} <small>({{ getRegion().name }})</small>
          </div>
          <v-btn icon @click="drawer = true" class="white--text">
            <v-icon>mdi-account</v-icon></v-btn
          ></v-toolbar-title
        >
      </v-toolbar>
    </nav>
    <nuxt />
    <Snackbar v-if="snackbarData" :show.sync="showSnackbar" :data.sync="snackbarData"/>
  </v-app>
</template>

<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import Snackbar from "../components/Snackbar";
export default {
  data() {
    return {
      drawer: false,
      snackbarData: null
    };
  },
  components: {
    Snackbar
  },
  computed: {
    snackbar() {
      return this.$store.getters["snackbar/getData"]
    }
  },
  methods: {
    getUser() {
      return this.$store.getters["getUser"]
        ? this.$store.getters["getUser"].fullName
        : "";
    },
    changeRegion() {
      this.$store.commit("setRegion", null);
      this.$router.push({
        path: "/login"
      });
    },
    getRegionName() {
      return this.getRegion() ? this.getRegion().name : "";
    },
    getToken() {
      return this.$store.getters["getToken"];
    },
    getRegion() {
      let region = this.$store.getters["getRegion"];
      return region;
    },
    async doLogout() {
      try {
        let response = await ApiServer.logout();
        this.$store.commit("setToken", null);
        this.$store.commit("setRegion", null);
        this.$router.push("/login");
      } catch (e) {}
    }
  },

  created() {
    setTimeout(() => {

      this.$store.commit("snackbar/setData", null);

      if (this.getToken()) {
        ApiServer.setToken(this.getToken());
        if (!this.getRegion()) {
          this.$router.push("login");
        }
      } else {
        this.$router.push("login");
      }
    });
  },

  watch: {
    snackbar: {
      handler: function(newVal, oldVal) {
        if(newVal) {
          this.snackbarData = newVal;
          this.showSnackbar = true;
          
        }
      }
    }
  }
};
</script>
