<template>
  <div v-if="!freeAccount()" class="ma-4 fill-height">
    <ChaptersList
      :chapters.sync="chapters"
      :classSpec="'elevation-3'"
      v-on:edit="openEditModal"
      v-if="!noChaptersFound"
    />

    <div v-else>
      Nessun capitolo trovato :(
    </div>

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
        v-on:close="showEditChapter = false"
        v-on:saveChapter="updateChapters"
      />
    </v-dialog>
    <v-btn fixed fab bottom right color="primary" @click="newChapter()">
      <v-icon>mdi-plus</v-icon>
    </v-btn>
  </div>
  <div v-else>
    Free account message
  </div>
</template>

<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import EditChapter from "../components/EditChapter";
import ChaptersList from "../components/ChaptersList";
export default {
  data() {
    return {
      showEditChapter: false,
      editChapter: null,
      chapters: [],
      users: [],
      regionId: null,
      noChaptersFound: false
    };
  },
  components: {
    EditChapter,
    ChaptersList
  },
  methods: {
    openEditModal(chapter) {
      this.editChapter = chapter;
      this.showEditChapter = true;
    },

    freeAccount() {
      return this.$store.getters["isFreeAccount"];
    },

    updateChapters(chapter) {
      this.showEditChapter = false;
      this.chapters.push(chapter);
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
      } else {
        this.chapters = response;
        this.$store.commit("setChapters", this.chapters);
      }
    }
  },
  created() {
    setTimeout(() => {
      this.regionId = this.$store.getters["getRegion"].id;
      this.fetchChapters();
      this.fetchUsersPerRegion();
    });
  }
};
</script>
