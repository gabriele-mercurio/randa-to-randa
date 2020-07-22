<template>
  <v-app>
    <nav v-if="getToken() && getRegion()" class="d-print-none">
      <v-navigation-drawer v-model="drawer" absolute temporary right>
        <v-list nav dense>
          <v-list-item-group active-class="secondary--text text--accent-4">
            <v-list-item to="/home">
              <v-list-item-icon>
                <v-icon>mdi-home</v-icon>
              </v-list-item-icon>
              <v-list-item-title>{{ $t("home") }}</v-list-item-title>
            </v-list-item>
            <!-- 
            <v-list-item to="/login">
              <v-list-item-icon>
                <v-icon>mdi-user-arrow-right-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-title>{{ $t("account") }}</v-list-item-title>
            </v-list-item> -->

            <v-list-item @click="changeRegion()">
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-title>{{ $t("change_region") }}</v-list-item-title>
            </v-list-item>

            <v-list-item @click="changePassword()">
              <v-list-item-icon>
                <v-icon>mdi-lock-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Cambia password</v-list-item-title>
            </v-list-item>

            <v-list-item @click="actAs()" v-if="canShowActAs()">
              <v-list-item-icon>
                <v-icon>mdi-account-switch-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-title>{{ $t("act_as") }}</v-list-item-title>
            </v-list-item>

            <v-list-item @click="backToAdmin()" v-if="canShowBackToAdmin()">
              <v-list-item-icon>
                <v-icon>mdi-account-switch-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-title>{{ $t("back_to_admin") }}</v-list-item-title>
            </v-list-item>

            <v-list-item @click="doLogout()">
              <v-list-item-icon>
                <v-icon>mdi-account-arrow-right</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Logout</v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-navigation-drawer>
      <v-toolbar color="primary" class="white--text">
        <v-btn class="white--text" text link to="/home">
          {{ $t("home") }}
        </v-btn>
        <v-btn
          class="white--text"
          text
          link
          to="/chapters"
          v-if="!isNational()"
        >
          {{ $t("chapters") }}
        </v-btn>
        <v-btn
          class="white--text"
          text
          link
          v-if="isNational()"
          to="/randa/randa-revised"
        >
          Approva randa
        </v-btn>
        <v-menu offset-y v-if="!isNational() && role != 'ASSISTANT'">
          <template v-slot:activator="{ on }">
            <v-btn class="white--text" text v-on="on">
              {{ $t("randa") }}
              <v-icon>mdi-menu-down</v-icon>
            </v-btn>
          </template>
          <v-list v-if="!isNational()">
            <v-list-item>
              <v-btn text to="/randa/randa-dream">
                {{ $t("randa_dream") }}
              </v-btn>
            </v-list-item>
            <v-list-item v-if="!isNational()">
              <v-btn text to="/randa/randa-revised">
                {{ $t("randa_revised") }}
              </v-btn>
            </v-list-item>
            <v-list-item v-if="!isNational()">
              <v-btn text to="/randa/randa">
                {{ $t("randa") }}
              </v-btn>
            </v-list-item>
          </v-list>
        </v-menu>
        <v-menu offset-y v-if="!freeAccount && !isNational()&& role != 'ASSISTANT' ">
          <template v-slot:activator="{ on }">
            <v-btn class="white--text" text v-on="on">
              {{ $t("directors") }}
              <v-icon>mdi-menu-down</v-icon>
            </v-btn>
          </template>
          <v-list v-if="!isNational()">
            <v-list-item>
              <v-btn text to="/directors">
                {{ $t("management") }}
              </v-btn>
            </v-list-item>
          </v-list>
        </v-menu>

        <v-btn
          class="white--text"
          text
          to="/economics"
          v-if="!freeAccount && !isNational() && role != 'ASSISTANT'"
        >
          Economics
        </v-btn>
        <v-spacer></v-spacer>
        <v-toolbar-title class="d-flex flex-row align-center">
          <div>
            {{ getUser() }}
            <small class="font-italic font-weight-light mr-2" v-if="freeAccount"
              >Account gratuito</small
            ><small v-if="!isNational()">({{ getRegion().name }})</small>
          </div>
          <v-btn icon @click="drawer = true" class="white--text">
            <v-icon>mdi-account</v-icon>
            </v-btn
          ></v-toolbar-title
        >
      </v-toolbar>
    </nav>
    <nuxt />

    <v-dialog v-model="promptUserChange" width="500" :scrollable="false">
      <v-card>
        <v-card-title class="headline primary white--text" primary-title>
          Seleziona director
        </v-card-title>
        <v-card-text class="pa-5">
          <v-select
            :items="regionDirectors"
            label="Seleziona director"
            v-model="actAsDirector"
            item-text="fullName"
            return-object
            required
            prepend-icon="mdi-account"
            @change="setActAs()"
          ></v-select>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="showChangePassword" width="500" :scrollable="false">
      <v-card>
        <v-card-title class="headline primary white--text" primary-title>
          Cambia password
        </v-card-title>
        <v-card-text class="pa-5">
          <span class="font-italic">La password deve contenere almeno 8 caratteri, tra cui una lettera e un numero.</span>
          <v-text-field
            v-model="pwd1"
            label="Password"
            required
            prepend-icon="mdi-lock-outline"
            type="password"
            :error-messages="invalidPwd1"
            @blur="checkPwds()"
          ></v-text-field>
          <v-text-field
            v-model="pwd2"
            label="Ripeti password"
            prepend-icon="mdi-lock-outline"
            type="password"
            :error-messages="invalidPwd2"
            @blur="checkPwds()"
          ></v-text-field>
          <div class="d-flex justify-end">
            <v-btn normal text  @click="showChangePassword = false">
              Annulla</v-btn
            >
            <v-btn  normal text  class="ml-4" color="primary" @click="doChangePassword()" :disabled="!pwdsSyntax(pwd1) || !pwdsSyntax(pwd2) || pwd1 !== pwd2">
              Conferma</v-btn
            >
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-snackbar v-model="showPwdSuccess" :timeout="timeout" top right>
      <v-icon color="green">mdi-check</v-icon>
      La password è stata correttamente modificata! Al prossimo accesso inserire la nuova password.
    </v-snackbar>

  </v-app>
</template>

<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import Snackbar from "../components/Snackbar";
import XLSX from "xlsx";

export default {
  data() {
    return {
      drawer: false,
      timeout: 3000,
      pwd_error: false,
      pwd_error_message: "",
      promptUserChange: false,
      actAsDirector: null,
      regionDirectors: [],
      message: "",
      showChangePassword: false,
      pwd1: "",
      pwd2: "",
      invalidPwd1: [],
      invalidPwd2: [],
      pwds_errors: false,
      showPwdSuccess: false,
      timeout: 5000,
      role: null
    };
  },
  craeted() {
    this.pwd1 = "";
    this.pwd2 = "";
    this.invalidPwd1 = [];
    this.invalidPwd2 = [];
  },
  components: {
    Snackbar
  },
  computed: {
    snackbar() {
      return this.$store.getters["snackbar/getData"];
    },
    freeAccount() {
      return this.$store.getters["isFreeAccount"];
    }
  },
  methods: {
    canShowBackToAdmin() {
      let actAs = this.$store.getters["getActAs"];
      let isAdmin = this.$store.getters["getOriginalUser"].isAdmin;
      return actAs && isAdmin;
    },

    checkPwds() {
      if (this.pwd1 && this.pwd2) {
        if (this.pwd1 !== this.pwd2) {
          this.invalidPwd1 = ["Le password non corrispondono"];
          this.invalidPwd2 = ["Le password non corrispondono"];
          this.pwds_errors = true;
        } else {
          if (this.pwdsSyntax(this.pwd1) && this.pwdsSyntax(this.pwd2)) {
            this.pwd_error = false;
            this.pwds_errors = false;
            this.invalidPwd1 = [];
            this.invalidPwd2 = [];
          }
        }
      } else {
        if (this.pwd1 && !this.pwdsSyntax(this.pwd1)) {
          this.invalidPwd1 = [
            "Inserire una password che abbia almeno 8 caratteri, tra cui una lettera e un numero."
          ];
        }
        if (this.pwd2 && !this.pwdsSyntax(this.pwd2)) {
          this.invalidPwd2 = [
            "Inserire una password che abbia almeno 8 caratteri, tra cui una lettera e un numero."
          ];
        }
      }
    },

    pwdsSyntax(pwd) {
      let regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
      let result = pwd.match(regex);
      return result;
    },

    changePassword() {
      this.showChangePassword = true;
    },

    async doChangePassword() {
      if (this.pwd1 !== this.pwd2) {
      }
      let result = await ApiServer.put("changePassword", {
        pwd1: this.pwd1,
        pwd2: this.pwd2
      });

      this.showChangePassword = false;

      if (result) {
        this.showPwdSuccess = true;
        
      } 
    },

    isNational() {
      return this.$store.getters["getIsNational"];
    },
    getUser() {
      return this.$store.getters["getUser"]
        ? this.$store.getters["getUser"].fullName
        : "";
    },
    backToAdmin() {
      this.$store.commit("setActAs", null);
      this.$store.commit("setUserRole", "ADMIN");
      this.$router.go();
    },
    setActAs() {
      this.$store.commit("setActAs", this.actAsDirector);
      this.$store.commit("setUserRole", this.actAsDirector.rtrole);
      this.snackbarState = "success";
      this.snackbarMessageLabel = "role_changed";
      this.showSnackbar = true;
      this.promptUserChange = false;
      this.$router.go();
    },
    canShowActAs() {
      return this.$store.getters["getUser"].isAdmin;
    },
    async fetchRegionDirectors() {
      let region = this.$store.getters["getRegion"].id;
      this.regionDirectors = await ApiServer.get(region + "/directors");
    },
    actAs() {
      this.fetchRegionDirectors();
      this.promptUserChange = true;
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
      if(this.$store) {
        console.log("STORE IS READU");
      } else {
        console.log("STORE IS not READU");
      }
      return this.$store.getters["getToken"];
    },
    getRegion() {
      let region = this.$store.getters["getRegion"];
      console.log(region);
      return region;
    },
    async doLogout() {
      try {
        let response = await ApiServer.logout();
        this.$store.commit("setToken", null);
        this.$store.commit("setRegion", null);
        this.$store.commit("setActAs", null);
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
          this.$router.push("/login");
        }
        this.role = this.$store.getters["getRegion"].role;
      } else {
        this.$router.push("/login");
      }
    });
  },

  watch: {
    snackbar: {
      handler: function(newVal, oldVal) {
        if (newVal) {
          this.snackbarData = newVal;
          this.showSnackbar = true;
        }
      }
    }
  }
};
</script>
