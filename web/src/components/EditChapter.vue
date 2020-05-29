<template>
  <v-card id="editChapter">
    <v-card-title class="headline primary white--text" primary-title>
      {{ getEditMode() }} Capitolo
    </v-card-title>
    <v-card-text class="pa-5">
      <form>
        <v-text-field
          v-model="chapter.name"
          label="Nome capitolo"
          required
          prepend-icon="mdi-tag"
        ></v-text-field>
        <!-- <v-select
          :items="states"
          label="Stato"
          v-model="chapter.currentState"
          required
          prepend-icon="mdi-progress-check"
        ></v-select> -->
        <MonthPicker
          :defLabel="'Data lancio core group'"
          :defDate.sync="chapter.coreGroupLaunchPrev"
          v-on:setdate="setCoreGroupLaunch"
          :disabled="isCoreGroupOrChapter(chapter)"
        />
        <MonthPicker
          :defLabel="'Data lancio capitolo'"
          :defDate.sync="chapter.chapterLaunchPrev"
          v-on:setdate="setChapterLaunch"
          :disabled="isChapter(chapter)"
        />
        <v-select
          :items="users"
          label="Seleziona assistant"
          v-model="chapter.director"
          item-text="fullName"
          return-object
          required
          prepend-icon="mdi-account"
        ></v-select>
      </form>
    </v-card-text>
    <v-card-actions class="d-flex justify-end align-center">
      <div width="100%">
        <v-btn type="submit" normal text color="primary" @click="emitClose()">
          Annulla
        </v-btn>
        <v-btn
          type="submit"
          normal
          text
          color="primary"
          @click="saveChapter()"
          :disabled="!isFormValid()"
        >
          Salva
        </v-btn>
      </div>
    </v-card-actions>
  </v-card>
</template>
<script>
import Utils from "../services/Utils";
import ApiServer from "../services/ApiServer";
import MonthPicker from "../components/MonthPicker";

const mandatoryFields = [
  "name",
  "currentState",
  "chapterLaunchPrev",
  "coreGroupLaunchPrev",
  "director"
];

const chapterSkeleton = {
  name: "",
  currentStatus: "PROJECT",
  chapterLaunch: {
    prev: null,
    actual: null
  },
  coreGroupLaunch: {
    prev: null,
    actual: null
  },
  director: null
};

export default {
  components: {
    MonthPicker
  },
  data() {
    return {
      editMode: false,
      chapter: {...chapterSkeleton},
      users: [],
      states: ["PROJECT", "CORE GROUP", "CHAPTER"]
    };
  },
  props: {
    editChapter: {
      type: Object,
      default: null
    }
  },
  methods: {
    getEditMode() {
      return this.editChapter ? "Modifica" : "Crea";
    },
    fetchUsers() {
      let region = this.$state.getters["getRegion"];
      //todo
      //this.users = ApiServer.get("users?region=" + region);
    },
    setChapterLaunch(value) {
      this.chapter.chapterLaunch.prev = value;
    },
    setCoreGroupLaunch(value) {
      this.chapter.coreGroupLaunch.prev = value;
    },
    emitClose() {
      this.chapter = {...chapterSkeleton};
      this.$emit("close");
    },
    saveChapter() {
      //todo
      ApiServer.post("chapter");
    },
    isCoreGroupOrChapter(item) {
      return (
        item.currentStatus === "CORE GROUP" || item.currentStatus === "CHAPTER"
      );
    },
    isChapter(item) {
      return item.currentStatus === "CHAPTER";
    },
    saveChapter() {
      console.log(this.chapter);
    },
    isFormValid() {
      Object.keys(this.chapter).forEach(k => {
        console.log(this.chapter[k]);
        if (mandatoryFields.indexOf(k) && !this.chapter[k]) {
          return false;
        }
      });
      return true;
    }
  },
  watch: {
    editChapter: {
      handler: function(newVal, oldVal) {
        if (this.editChapter) {
          this.chapter = { ...this.editChapter };
          this.chapter["coreGroupLaunchPrev"] = Utils.getMonthYear(
            this.chapter["coreGroupLaunch"]["prev"]
          );
          this.chapter["chapterLaunchPrev"] = Utils.getMonthYear(
            this.chapter["chapterLaunch"]["prev"]
          );
        }
      },
      deep: true,
      immediate: true
    }
  }
};
</script>
<style lang="scss">
#editChapter {
  .v-icon {
    font-size: 18px !important;
  }
}
</style>
