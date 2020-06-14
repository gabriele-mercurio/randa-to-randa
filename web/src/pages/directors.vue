<template>
  <div class="ma-4 fill-height">
    <DirectorsList v-on:edit="openEditModal" :directors.sync="directors" />
    <v-dialog
      :persistent="false"
      v-model="showEdit"
      width="900"
      :scrollable="false"
    >
      <EditDirector
        :show="showEdit"
        :editDirector.sync="editDirector"
        :areaDirectors="areaDirectors"
        v-on:close="showEdit = false"
        v-on:saveDirector="saveDirector"
      />
    </v-dialog>
    <v-btn
      fixed
      fab
      bottom
      right
      color="primary"
      @click="newDirector()"
      v-if="canCreateDirector()"
    >
      <v-icon>mdi-plus</v-icon>
    </v-btn>

    <v-snackbar v-model="successSnackbar" :timeout="timeout" top right>
      <v-icon color="green">mdi-check</v-icon>
      {{ snackbarMessage }}
      <v-btn color="white" icon @click="successSnackbar = false">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-snackbar>
  </div>
</template>

<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import EditDirector from "../components/EditDirector";
import DirectorsList from "../components/DirectorsList";
export default {
  data() {
    return {
      showEdit: false,
      editDirector: null,
      directors: [],
      areaDirectors: [],
      successSnackbar: false,
      snackbarMessage: "",
      timeout: 3000
    };
  },
  props: {},
  components: {
    EditDirector,
    DirectorsList
  },
  methods: {
    openEditModal(director) {
      this.editDirector = director;
      this.showEdit = true;
    },
    newDirector() {
      this.editDirector = null;
      this.showEdit = true;
    },
    async canCreateDirector() {
      let user = await this.$store.getters["getUser"];
      return (
        user.isAdmin || user.role === "NATIONAL" || user.role === "EXECUTIVE"
      );
    },
    async fetchAreaDirectors() {
      try {
        let region = await this.$store.getters["getRegion"].id;
        this.areaDirectors = await ApiServer.get(
          region + "/directors?onlyArea=true"
        );
      } catch (e) {
        console.log(e);
      }
    },
    saveDirector(e) {
      this.snackbarSuccess = true;
      this.snackbarMessage = "Director creato correttamente!";
    }
  },
  async created() {
    setTimeout(() => {
      this.fetchAreaDirectors();
    });
  }
};
</script>
