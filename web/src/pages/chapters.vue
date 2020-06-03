<template>
  <div class="ma-4 fill-height">
    <ChaptersList v-on:edit="edit(item)" />

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
      editChapter: null
    };
  },
  props: {},
  middleware: "auth",
  components: {
    EditChapter,
    ChaptersList
  },
  methods: {
    edit(chapter) {
      this.editChapter = chapter;
      this.showEditChapter = true;
    },

    newChapter() {
      this.editChapter = null;
      this.showEditChapter = true;
    }
  }
};
</script>
<style lang="scss">
.PROJECT {
  background-color: lighten(red, 40);
}

.CORE_GROUP {
  background-color: lighten(orange, 40);
}
</style>
