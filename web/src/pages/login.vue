<template>
  <v-container class="primary pa-0 ma-0" id="container">
    <h2 class="white--text text-center py-12">Accedi a ROSBI</h2>
    <v-form v-if="!isLogged" @submit.prevent="doLogin()">
      <v-card class="mx-auto mb-12 elevation-12" max-width="374">
        <v-card-title class="secondary--text text-center">Login</v-card-title>
        <v-card-text class="pb-0">
          <!-- <v-text-field
              label="Username"
              prepend-icon="mdi-face"
              color="purple"
          ></v-text-field>-->
          <v-text-field label="Email" prepend-icon="mdi-face" color="secondary" v-model="email"></v-text-field>
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
            <v-btn type="submit" normal text color="primary">Accedi</v-btn>
          </v-row>
        </v-card-actions>
        <div class="d-flex justify-center pb-3" @click="promptEmail()">
          <small class="font-italic font-weight-light link">Password dimenticata?</small>
        </div>
      </v-card>
    </v-form>
    <v-form v-else @submit.prevent="selectRegion()">
      <v-card class="mx-auto mb-12 elevation-12" max-width="374">
        <v-card-title class="secondary--text text-center">Seleziona region</v-card-title>
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
      <v-icon color="primary">mdi-alert</v-icon>Username o password errate
      <v-btn color="white" icon @click="error = false">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-snackbar>

    <v-snackbar v-model="passwordRecoveredMessage" :timeout="timeout" top right>
      <v-icon color="green">mdi-check</v-icon>Ti abbiamo inviato una mail all'indirizzo indicato!
      <v-btn color="white" icon @click="passwordRecoveredMessage = false">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-snackbar>
    <v-snackbar v-model="passwordRecoveredMessageError" :timeout="timeout" top right>
      <v-icon color="primary">mdi-alert</v-icon>L'indirizzo email inserito non è presente nel sistema...
      <v-btn color="white" icon @click="passwordRecoveredMessageError = false">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-snackbar>

    <div class="d-flex justify-end"></div>

    <v-dialog v-model="showPromptEmail" width="500" :scrollable="false">
      <v-card>
        <v-card-title class="headline primary white--text" primary-title>Resetta password</v-card-title>
        <v-card-text class="pa-5">
          <div
            class="font-weight-light font-italic pb-10"
          >Immetti il tuo indirizzo di posta ROSBI, ti verrà inviata una password che potrai cambiare una volta loggato.</div>
          <v-text-field
            v-model="resetPasswordEmail"
            label="Indirizzo email"
            required
            prepend-icon="mdi-email-outline"
            class="pa-0"
          ></v-text-field>
        </v-card-text>
        <v-card-actions class="d-flex pa-5">
          <div class="full-width d-flex justify-end align-end">
            <v-btn normal @click="showPromptEmail=false">Annulla</v-btn>
            <v-btn class="ml-3" normal color="primary" @click="resetPassword()">Ok</v-btn>
          </div>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
      isLogged: false,
      logo: require("@/assets/images/logo_mercurio.png"),
      showPromptEmail: false,
      resetPasswordEmail: "",
      passwordRecoveredMessage: false,
      passwordRecoveredMessageError: false,
    };
  },
  created() {
    setTimeout(() => {
      if (this.getToken()) {
        this.isLogged = true;
        if (this.getRegion()) {
          this.$router.push("/home");
        } else {
          this.fetchRegions();
        }
      }
    });
  },
  methods: {
    promptEmail() {
      if (this.email) {
        this.resetPasswordEmail = this.email;
      }
      this.showPromptEmail = true;
    },
    async resetPassword() {
      let response = await ApiServer.post("resetPassword", {
        email: this.resetPasswordEmail,
      });
      if (!response.error) {
        this.passwordRecoveredMessage = true;
        this.showPromptEmail = false;
      } else {
        this.passwordRecoveredMessageError = true;
      }
    },
    async doLogin() {
      Utils.removeFromStorage("region");
      let response = await ApiServer.login(this.email, this.password);
      if (response["token"] && response["user"]) {
        this.$store.commit("setUser", response["user"]);
        this.$store.commit("setToken", response["token"]);
        await this.fetchRegions();
        let isNational = false;
        for (let region of this.regions) {
          if (!isNational) {
            if (region.role === "NATIONAL") {
              isNational = true;
              this.$store.commit("setRegion", region);
            }
          }
        }
        if (isNational) {
          this.$store.commit("setIsNational", true);
          this.$router.push("/home");
        } else {
          this.$store.commit("setIsNational", null);
          if (this.regions.length === 1) {
            this.region = this.regions[0];
            this.selectRegion();
          }
        }
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
        path: "/chapters",
      });
    },

    async fetchRegions() {
      this.regions = await ApiServer.get("api/" + "regions");
    },

    selectRegion() {
      this.$store.commit("setRegion", this.region);
      this.goToHome();
    },
  },
};
</script>
<style lang="scss">
#container {
  width: 100%;
  max-width: 100%;
  height: 100%;
}

.full-width {
  width: 100%;
}
</style>
