<template>
  <v-container>
    <v-card class="mx-auto my-12 outer-card" max-width="374">
      <v-card-title>
        <v-card class="v-header py-2 mx-auto my-n12" max-width="364" raised>
          <v-card-title>
            <v-row justify="center" color="white">
              <h4>Login</h4>
            </v-row>
          </v-card-title>
          <v-card-actions>
            <v-row justify="center" color="white">
              <v-btn icon color="white">
                <v-icon>mdi-facebook</v-icon>
              </v-btn>
              <v-btn icon color="white">
                <v-icon>mdi-twitter</v-icon>
              </v-btn>
              <v-btn icon color="white">
                <v-icon>mdi-google-plus</v-icon>
              </v-btn>
            </v-row>
          </v-card-actions>
        </v-card>
      </v-card-title>
      <v-card-text class="my-4">
        <v-text-field
          label="Username"
          prepend-icon="mdi-face"
          color="purple"
        ></v-text-field>
        <v-text-field
          label="Email"
          prepend-icon="mdi-email"
          color="purple"
        ></v-text-field>
        <v-text-field
          label="Password"
          prepend-icon="mdi-lock-outline"
          color="purple"
        ></v-text-field>
      </v-card-text>
      <v-card-actions>
        <v-row justify="center">
          <v-btn normal text color="primary"> Accedi </v-btn>
        </v-row>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script>
import ApiServer from "../services/ApiServer";

export default {
  data() {
    return {
      username: "sara",
      password: "pupi"
    };
  },
  created() {
    //this.doLogin();
  },
  methods: {
    prependClicked() {
      console.log("prepend clicked");
    },
    appendClicked() {
      console.log("append clicked", this.username);
    },
    async doLogin() {
      try {
        let loginData = {
          username: this.username,
          password: this.password,
          grant_type: "password",
          client_id: process.env.client_id,
          client_secret: process.env.client_secret
        };
        let response = await this.$auth.loginWith("local", { data: loginData });
      } catch (e) {
        console.log(e);
      }
    }
  }
};
</script>
