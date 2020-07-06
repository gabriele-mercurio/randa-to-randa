<template>
  <div class="ma-4 fill-height">
    <template>
      <div class="d-flex flex-column" v-if="randa_info">
        <span><span class="font-weight-black">Randa</span>: {{ randa_info.timeslot }} {{randa_info.year}}</span>
        <span>{{ getState(randa_info.state) }}</span>
      </div>
      <ChaptersList
        :chapters.sync="chapters"
        :classSpec="'elevation-3'"
        v-on:edit="openEditModal"
        v-if="!noChaptersFound"
      />

      <div v-else>
        <NoData :message="'Nessun capitolo trovato'" />
        Nessun capitolo trovato :(
      </div>
    </template>

    <v-dialog
      :persistent="false"
      v-model="showEditChapter"
      width="500"
      :scrollable="false"
    >
      <EditChapter
        :show="showEditChapter"
        :editChapter.sync="editChapter"
        :users="users"
        :freeAccount.sync="freeAccount"
        v-on:close="showEditChapter = false"
        v-on:saveChapter="updateChapters"
      />
    </v-dialog>
    <v-btn fixed fab bottom right color="primary" @click="newChapter()">
      <v-icon>mdi-plus</v-icon>
    </v-btn>
  </div>
</template>

<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import ForbiddenPage from "../components/ForbiddenPAge";
import EditChapter from "../components/EditChapter";
import ChaptersList from "../components/ChaptersList";
import NoData from "../components/NoData";

export default {
  data() {
    return {
      showEditChapter: false,
      editChapter: null,
      chapters: [],
      users: [],
      regionId: null,
      noChaptersFound: false,
      freeAccount: false,
      forbiddenPage: false,
      randa_info: null
    };
  },
  components: {
    EditChapter,
    ChaptersList,
    ForbiddenPage,
    NoData
  },
  methods: {
    openEditModal(chapter) {
      this.editChapter = chapter;
      this.showEditChapter = true;
    },

    getState(randa_state) {
      return Utils.getRandaState(randa_state);
    },

    updateChapters(chapter) {
      this.showEditChapter = false;
      this.chapters.push(chapter);
      if (this.freeAccount) {
        this.$router.push("/rana/" + chapter.id);
      }
    },

    newChapter() {
      this.editChapter = null;
      this.showEditChapter = true;
    },

    async fetchUsersPerRegion() {
      this.users = await ApiServer.get(this.regionId + "/users");
    },

    async fetchChapters() {
      let response = await ApiServer.get(this.regionId + "/chapters");
      if (response.errorCode === 404) {
        this.noChaptersFound = true;
      } else if (response.errorCode === "403") {
        this.forbiddenPage = true;
      } else {
        this.chapters = response.chapters;
        this.randa_info = response.randa;
        this.$store.commit("setChapters", this.chapters);
      }
    }
  },
  created() {
    setTimeout(async () => {
      this.regionId = this.$store.getters["getRegion"].id;
      let role = this.$store.getters["getRegion"].role;
      if (role !== "NATIONAL") {
        this.fetchChapters();
        this.fetchUsersPerRegion();
      }
    });
  }
};
</script>
