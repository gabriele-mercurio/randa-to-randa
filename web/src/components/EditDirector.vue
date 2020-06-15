<template>
  <v-card id="editDirector">
    <v-card-title class="headline primary white--text" primary-title>
      {{ getEditModeString() }}  {{$t('director')}}
    </v-card-title>
    <v-card-text class="pa-5">
      <form>
        <v-row>
          <v-col cols="12" class="py-0 my-0">
            <v-combobox
              v-model="director.email"
              :items="users"
              :search-input.sync="search"
              @change="selectUser()"
              label="Email director"
              prepend-icon="mdi-at"
              autocomplete="new"
              outlined
              dense
              :rules="[rules.email]"
            ></v-combobox>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="6" class="py-0 my-0">
            <v-text-field
              v-model="director.firstName"
              label="Nome"
              required
              prepend-icon="mdi-face-outline"
              :disabled="isExistingUser"
              autocomplete="new"
            ></v-text-field>
          </v-col>
          <v-col cols="6" class="py-0 my-0">
            <v-text-field
              v-model="director.lastName"
              label="Cognome"
              required
              :disabled="isExistingUser"
              autocomplete="new"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="6" class="py-0 my-0">
            <v-select
              :items="roles"
              label="Seleziona ruolo"
              v-model="director.role"
              required
              prepend-icon="mdi-tie"
            ></v-select>
          </v-col>

          <v-col
            cols="6"
            class="py-0 my-0"
            v-if="director.role === 'ASSISTANT'"
          >
            <v-select
              :items="areaDirectors"
              label="Seleziona supervisore"
              v-model="director.supervisor"
              required
              prepend-icon="mdi-account-cog-outline"
              item-text="fullName"
              item-value="id"
            ></v-select>
          </v-col>

          <v-col
            cols="6"
            class="py-0 my-0"
            v-if="director.role === 'AREA' || director.role === 'EXECUTIVE'"
          >
            <v-text-field
              v-model="director.areaPercentage"
              label="Compenso area"
              required
              prepend-icon="mdi-margin"
              type="number"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="6" class="py-0 my-0">
            <v-checkbox
              v-model="director.isFreeAccount"
              label="Account free"
            ></v-checkbox>
          </v-col>
          <v-col cols="6" class="py-0 my-0" v-if="isAdmin()">
            <v-checkbox
              v-model="director.isAdmin"
              :disabled="director.isFreeAccount"
              label="Amministratore"
            ></v-checkbox>
          </v-col>
        </v-row>
        <v-divider></v-divider>
        <v-row class="d-flex">
          <v-col cols="6" class="py-0 my-0">
            <v-container fluid class="px-0">
              <p>Tipo di paga</p>
              <v-radio-group
                row
                v-model="director.payType"
                :disabled="director.isFreeAccount"
              >
                <v-radio label="Mensile" value="MONTHLY"></v-radio>
                <v-radio label="Annuale" value="ANNUAL"></v-radio>
              </v-radio-group>
            </v-container>
          </v-col>
          <v-col cols="3" class="py-0 my-0">
            <v-container fluid class="px-0">
              <p>Percentuale di lancio</p>
              <v-text-field
                v-model="director.launchPercentage"
                required
                prepend-icon="mdi-margin"
                class="pt-0 mt-0"
                :disabled="director.isFreeAccount"
                type="number"
              ></v-text-field>
            </v-container>
          </v-col>
          <v-col cols="3" class="py-0 my-0">
            <v-container fluid class="px-0">
              <p>Percentuale fissa</p>
              <v-text-field
                v-model="director.fixedpercentage"
                required
                prepend-icon="mdi-margin"
                class="pt-0 mt-0"
                type="number"
                :disabled="director.isFreeAccount"
              ></v-text-field>
            </v-container>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="3" class="py-0 my-0">
            <v-text-field
              v-model="director.greenLightPercentage"
              label="Green light"
              required
              :disabled="director.isFreeAccount"
            >
              <template v-slot:prepend>
                <LightCircle :color="'green'" />
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="3" class="py-0 my-0">
            <v-text-field
              v-model="director.yellowLightPercentage"
              :disabled="director.isFreeAccount"
              label="Yellow light"
              required
            >
              <template v-slot:prepend>
                <LightCircle :color="'yellow'" />
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="3" class="py-0 my-0">
            <v-text-field
              v-model="director.redLightPercentage"
              label="Red light"
              :disabled="director.isFreeAccount"
              required
            >
              <template v-slot:prepend>
                <LightCircle :color="'red'" />
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="3" class="py-0 my-0">
            <v-text-field
              :disabled="director.isFreeAccount"
              v-model="director.greyLightPercentage"
              label="Grey light"
              required
            >
              <template v-slot:prepend>
                <LightCircle :color="'grey'" />
              </template>
            </v-text-field>
          </v-col>
        </v-row>
      </form>
    </v-card-text>
    <v-card-actions class="d-flex justify-end align-center">
      <div width="100%">
        <v-btn type="submit" normal text color="primary" @click="emitClose()">
          {{$t('cancel')}}
          
        </v-btn>
        <v-btn
          type="submit"
          normal
          text
          color="primary"
          @click="saveDirector()"
          :disabled="!isFormValid()"
        >
          {{$t('save')}}
        </v-btn>
      </div>
    </v-card-actions>
    <v-snackbar v-model="errorSnackbar" :timeout="timeout" top right>
      <v-icon color="primary">mdi-alert</v-icon>
      {{ snackbarMessage }}
      <v-btn color="white" icon @click="errorSnackbar = false">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-snackbar>
  </v-card>
</template>
<script>
import Utils from "../services/Utils";
import ApiServer from "../services/ApiServer";
import LightCircle from "../components/LightCircle";

const directorSkeleton = {
  data: null,
  firstName: null,
  lastName: null,
  email: null,
  isAdmin: null,
  role: null,
  isFreeAccount: null,
  region: null,
  supervisor: null,
  payType: "MONTHLY",
  launchPercentage: null,
  greenLightPercentage: null,
  yellowLightPercentage: null,
  redLightPercentage: null,
  greyLightPercentage: null,
  fixedPercentage: null
};

const roles = ["ASSISTANT", "AREA", "EXECUTIVE", "NATIONAL"];

const mandatoryFields = ["firstName", "lastName", "email", "role", "payType"];

export default {
  data() {
    return {
      editMode: false,
      director: { ...directorSkeleton },
      coreGroupMessage: "",
      chapterMessage: "",
      roles: roles,
      users: [],
      regions: [],
      search: "",
      isSearching: false,
      hashedUsers: {},
      isExistingUser: false,
      snackbarMessage: "",
      successSnackbar: false,
      errorSnackbar: false,
      timeout: 3000,
      rules: {
        email: val => {
          const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(val) || this.$t('invalid_email');
        }
      }
    };
  },
  props: {
    editDirector: {
      type: Object,
      default: null
    },
    areaDirectors: {
      type: Array,
      default: null
    }
  },
  components: {
    LightCircle
  },
  computed: {
    isEditing() {
      return this.editMode;
    },
    isCreating() {
      return !this.editMode;
    }
  },
  methods: {
    setData() {
      if (this.director.data) {
        this.director.firstName = this.director.data.firstName;
        this.director.lastName = this.director.data.lastName;
        this.director.email = this.director.email;
      } else {
        this.director.email = this.search;
      }
    },
    isAdmin() {
      return this.$store.getters["getUser"].isAdmin;
    },
    async doAutocomplete(term) {
      let users = await ApiServer.get("user/search?term=" + term);
      if (users) {
        this.users = users.map(user => {
          this.hashedUsers[user.value] = user;
          user = user.value;
          return user;
        });
      }
    },
    selectUser() {
      let hashedUser = this.hashedUsers[this.director.email];
      if (hashedUser) {
        this.director.firstName = hashedUser.firstName;
        this.director.lastName = hashedUser.lastName;
        this.isExistingUser = true;
      } else {
        this.director.firstName = null;
        this.director.lastName = null;
        this.isExistingUser = false;
      }
    },
    getEditModeString() {
      return this.editChapter ? this.$t('edit') : this.$t('create');
    },
    fetchUsers() {
      let region = this.$state.getters["getRegion"];
      //todo
      //this.users = ApiServer.get("users?region=" + region);
    },
    emitClose() {
      this.director = { ...directorSkeleton };
      this.$emit("close");
    },
    async saveDirector() {
      let data = {};
      data = { ...this.director };
      if (data.role !== "ASSISTANT") {
        data.supervisor = null;
      }

      let region = this.$store.getters["getRegion"].id;
      let response = this.editMode
        ? await ApiServer.put("director/" + this.director.id, data)
        : (response = await ApiServer.post(region + "/director", data));

      if (!response.error) {
        this.successSnackbar = true;
        if (this.editMode) {
          this.snackbarMessage = this.$t('director') + " " + this.$t('successfuly_edited');
        } else {
          this.snackbarMessage = this.$t('director') + " " + this.$t('successfuly_created');
        }
        this.emitClose();
        let directors = this.$store.getters["directors/getDirectors"];
        directors.push(response);
        this.$store.commit("directors/setDirectors", directors);

        this.$emit("saveDirector", response);
      } else {
        this.errorSnackbar = true;
        if (response.errorCode == 422) {
          this.snackbarMessage = this.$t("user_with_role_exists");
        } else {
          this.snackbarMessage = response.message;
        }
      }
    },
    isFormValid() {
      for (let k of Object.keys(this.director)) {
        if (mandatoryFields.includes(k) && !this.director[k]) {
          return false;
        }
      }
      if (this.director.role === "ASSISTANT" && !this.director.supervisor) {
        return false;
      }
      return true;
    },

    async fetchAreaDirectors() {
      this.areaDirectors = await ApiServer.get("users");
    }
  },
  created() {},
  watch: {
    editDirector: {
      handler: function(newVal, oldVal) {
        if (this.editDirector) {
          this.editMode = true;
          this.director = { ...this.editDirector };
          this.director.firstName = this.director.fullName;
        } else {
          this.editMode = false;
        }
      },
      deep: true,
      immediate: true
    },
    search: {
      handler: function(newVal, oldVal) {
        if (newVal && newVal.length > 2) {
          this.doAutocomplete(newVal);
        }
      }
    }
  }
};
</script>
