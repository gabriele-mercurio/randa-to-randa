<template>
  <div class="ma-4 fill-height">
    <DirectorsList v-on:edit="openEditModal" />
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
      />
    </v-dialog>
    <v-btn fixed fab bottom right color="primary" @click="newDirector()">
      <v-icon>mdi-plus</v-icon>
    </v-btn>
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
      areaDirectors: []
    }
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
    async fetchAreaDirectors() {
      try {
        this.areaDirectors = await ApiServer.get(this.$store.getters["getRegion"].id + "/directors?onlyArea=true");
      } catch(e) {

      }
    }
  },
  created() {
    this.fetchAreaDirectors();  
  }
};
</script>
