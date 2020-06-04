<template>
  <div class="ma-4 fill-height">
    <ChaptersList :chapters.sync="chapters" :classSpec="'elevation-3'" v-on:edit="edit(item)" />

    <v-dialog
      :persistent="false"
      v-model="showEditChapter"
      width="500"
      :scrollable="false"
    >
      <EditChapter
        :show="showEditChapter"
        :editChapter.sync="editChapter"
        v-on:close="showEditChapter = false"
        v-on:saveChapter="updateChapters()"
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
      chapters: []
    };
  },
  props: {},
  components: {
    EditChapter,
    ChaptersList
  },
  methods: {
    edit(chapter) {
      this.editChapter = chapter;
      this.showEditChapter = true;
    },

    updateChapters(chapter) {
      this.showEditChapter = false;
      this.chapters.push(chapter);
    },

    newChapter() {
      this.editChapter = null;
      this.showEditChapter = true;
    },

     async fetchChapters() {
      if (this.$store.getters["getRegion"]) {
        let role = this.$store.getters["getRole"]
          ? "?role=" + this.$store.getters["getRole"]
          : "";
        this.chapters = await ApiServer.get(
          this.$store.getters["getRegion"].id + "/chapters" + role
        );
      }

      // console.log(this.chapters);
      // this.chapters = await Promise.resolve([
      //   {
      //     chapterLaunch: {
      //       prev: "2008-03",
      //       actual: null
      //     },
      //     closureDate: "string",
      //     coreGroupLaunch: {
      //       prev: "2020-09",
      //       actual: "2020-12"
      //     },
      //     currentState: "PROJECT",
      //     director: {
      //       id: 0,
      //       fullName: "Luigi luigetti"
      //     },
      //     id: 0,
      //     members: 10,
      //     name: "Abn",
      //     suspDate: null,
      //     warning: "CHAPTER"
      //   },
      //   {
      //     chapterLaunch: {
      //       prev: "2018-04",
      //       actual: "2020-05"
      //     },
      //     closureDate: null,
      //     coreGroupLaunch: {
      //       prev: "2020-08",
      //       actual: "2020-07"
      //     },
      //     currentState: "PROJECT",
      //     director: {
      //       id: 0,
      //       fullName: "Luigi poi"
      //     },
      //     id: 1,
      //     members: 100,
      //     name: "Saracap",
      //     suspDate: null,
      //     warning: null
      //   }
      // ]);
    },
  },
  created() {
    this.fetchChapters();
  }
};
</script>
