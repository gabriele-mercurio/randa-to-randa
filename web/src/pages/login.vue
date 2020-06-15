<template>
  <v-container class="primary pa-0 ma-0" id="container">
    <h2 class="white--text text-center py-12">Accedi a Randa to Randa</h2>
    <v-form v-if="!isLogged" @submit.prevent="doLogin()">
      <v-card class="mx-auto mb-12 elevation-12" max-width="374">
        <v-card-title class="secondary--text text-center">
          Login
        </v-card-title>
        <v-card-text class="my-4">
          <!-- <v-text-field
              label="Username"
              prepend-icon="mdi-face"
              color="purple"
            ></v-text-field> -->

          <v-text-field
            label="Email"
            prepend-icon="mdi-face"
            color="secondary"
            v-model="email"
          ></v-text-field>
          <v-text-field
            label="Password"
            prepend-icon="mdi-lock-outline"
            color="secondary"
            v-model="password"
            type="password"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-row justify="center">
            <v-btn type="submit" normal text color="primary"> Accedi </v-btn>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-form>
    <v-form v-else @submit.prevent="selectRegion()">
      <v-card class="mx-auto mb-12 elevation-12" max-width="374">
        <v-card-title class="secondary--text text-center">
          Seleziona region
        </v-card-title>
        <v-card-text class="my-4">
          <v-select
            :items="regions"
            label="Seleziona region"
            v-model="region"
            @change="selectRegion()"
            item-text="name"
            return-object
          ></v-select>
        </v-card-text>
      </v-card>
    </v-form>

    <v-snackbar v-model="error" :timeout="timeout" top right>
      <v-icon color="primary">mdi-alert</v-icon>
      Username o password errate
      <v-btn color="white" icon @click="error = false">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-snackbar>
  </v-container>
</template>

<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";

export default {
  data() {
    return {
      email: "",
      password: "",
      error: false,
      regions: [],
      regionId: null,
      timeout: 3000,
      region: null,
      isLogged: false
    };
  },
  created() {
    setTimeout(() => {
      if (this.getToken()) {
        this.isLogged = true;
        if (this.getRegion()) {
          this.$router.push("home");
        } else {
          this.fetchRegions();
        }
      }
    });
  },
  methods: {
    async doLogin() {
      Utils.removeFromStorage("region");
      let response = await ApiServer.login(this.email, this.password);
      if (response["token"] && response["user"]) {
        this.$store.commit("setUser", response["user"]);
        this.$store.commit("setToken", response["token"]);
        this.fetchRegions();
        this.isLogged = true;
      } else {
        this.error = true;
      }
    },

    getToken() {
      return this.$store.getters["getToken"];
    },
    getRegion() {
      return this.$store.getters["getRegion"];
    },

    goToHome() {
      this.$router.push({
        path: "/chapters"
      });
    },

    async fetchRegions() {
      this.regions = await ApiServer.get("regions");
    },

    selectRegion() {
      this.$store.commit("setRegion", this.region);
      this.goToHome();
    }
  }
};
</script>
<style scoped>
#container {
  width: 100%;
  max-width: 100%;
  height: 100%;
}
</style>
