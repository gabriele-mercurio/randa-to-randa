<template>
  <v-container id="login-container" class="primary">
    <h2 class="white--text text-center py-12">Accedi a Randa to Randa</h2>
    <v-form @submit.prevent="doLogin()">
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

    <v-snackbar v-model="error" timeout=3000 top right>
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

export default {
  data() {
    return {
      email: "",
      password: "",
      error: false
    };
  },
  created() {
    if (this.$auth.loggedIn) {
      if(localStorage.getItem("region")) {
        this.goToHome();
      } else {
        this.goToRegionSelection();
      }
    }
  },
  methods: {
    async doLogin() {
      try {
        let loginData = {
          email: this.email,
          password: this.password,
          grant_type: "password",
          client_id: process.env.client_id,
          client_secret: process.env.client_secret
        };
        let response = await this.$auth.loginWith("local", { data: loginData });
        this.goToRegionSelection();
      } catch (e) {
        this.error = true;
      }
    },

    goToHome() {
      this.$router.push({
        path: "/home"
      });
    },
    goToRegionSelection() {
      this.$router.push({
        path: "/region-selection"
      });
    }
  }
};
</script>
<style scoped>
#login-container {
  height: 100%;
}
</style>
