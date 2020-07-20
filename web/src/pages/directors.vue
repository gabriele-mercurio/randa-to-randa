<template>
  <div class="ma-4 fill-height">
    <DirectorsList
      v-on:edit="openEditModal"
      :directors.sync="directors"
      v-if="!noDirectorsFound && canSeeDirectors()"
    />

    <NoData v-else :message="'Nessun director da mostrare'" />

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
import NoData from "../components/NoData";

export default {
  data() {
    return {
      showEdit: false,
      editDirector: null,
      directors: [],
      areaDirectors: [],
      successSnackbar: false,
      snackbarMessage: "",
      timeout: 3000,
      noDirectorsFound: false
    };
  },
  props: {},
  components: {
    EditDirector,
    DirectorsList,
    NoData
  },
  methods: {
    canSeeDirectors() {
      return this.$store.getters["getRegion"].role === "ADMIN" || this.$store.getters["getRegion"].role === "EXECUTIVE";
    },
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
      if (user) {
        return (
          user.isAdmin || user.role === "NATIONAL" || user.role === "EXECUTIVE"
        );
      }
    },
    async fetchAreaDirectors() {
      try {
        let region = await this.$store.getters["getRegion"].id;
        this.areaDirectors = await ApiServer.get(
          region + "/directors?onlyArea=1"
        );
      } catch (e) {
        console.log(e);
      }
    },
    async fetchDirectors() {
      let response = await ApiServer.get(
        this.$store.getters["getRegion"].id + "/directors"
      );
      if (response.errorCode === 404) {
        this.noDirectorsFound = true;
      } else {
        this.directors = response;
        this.$store.commit("directors/setDirectors", this.directors);
      }
    },
    saveDirector(response) {
      this.successSnackbar = true;
      this.fetchDirectors();
      this.snackbarMessage = response.edtiMode
        ? this.$t("director_edited")
        : this.$t("director_created");
    }
  },
  async created() {
    setTimeout(() => {
      this.fetchAreaDirectors();
      this.fetchDirectors();
    });
  }
};
</script>
