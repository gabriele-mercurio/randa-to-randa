<template>
  <div class="ma-4 fill-height">
    <ChaptersList
      :chapters.sync="chapters"
      :classSpec="'elevation-3'"
      v-on:edit="openEditModal"
    />

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
      regionId: null
    }
  },
  props: {},
  components: {
    EditChapter,
    ChaptersList
  },
  methods: {
    openEditModal(chapter) {
      debugger;
      this.editChapter = chapter;
      this.showEditChapter = true;
    },

    updateChapters(chapter) {
      this.showEditChapter = false;
      this.chapters.push(chapter);
      console.log(this.chapters[this.chapters.length - 2].chapterLaunch);
      console.log(this.chapters[this.chapters.length - 1].chapterLaunch);
      debugger;
    },

    newChapter() {
      this.editChapter = null;
      this.showEditChapter = true;
    },

    async fetchUsersPerRegion() {
      this.users = await ApiServer.get(this.regionId + "/users");
    },

    async fetchChapters() {
        this.chapters = await ApiServer.get(this.regionId + "/chapters");
    }
  },
  created() {
      this.regionId = Utils.getFromStorage("region").id;
      this.fetchChapters();
      this.fetchUsersPerRegion();
  }
};
</script>
