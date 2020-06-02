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
          :defLabel="getCoreGroupLabel(chapter)"
          :defDate.sync="chapter.coreGroupLaunch"
          v-on:setdate="setCoreGroupLaunch"
          :disabled="isCoreGroupOrChapter(chapter)"
        />
        <span v-if="chapter.coreGroupLaunchWarning"
          >Attenzione, si sta inserendo una data di lancio passata; il capitolo
          verrà creato in stato core group.</span
        >
        <MonthPicker
          :defLabel="getChapterLabel(chapter)"
          :defDate.sync="chapter.chapterLaunch"
          v-on:setdate="setChapterLaunch"
          :disabled="isChapter(chapter)"
        />
        <span v-if="chapter.chapterLaunchWarning"
          >Attenzione, si sta inserendo una data di lancio passata; il capitolo
          verrà creato in stato capitolo.</span
        >
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
  currentState: "PROJECT",
  chapterLaunch: null,
  coreGroupLaunch: null,
  director: null
};

export default {
  components: {
    MonthPicker
  },
  data() {
    return {
      editMode: false,
      chapter: { ...chapterSkeleton },
      users: [],
      states: ["PROJECT", "CORE_GROUP", "CHAPTER"]
    };
  },
  props: {
    editChapter: {
      type: Object,
      default: null
    }
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
    getEditMode() {
      return this.editChapter ? "Modifica" : "Crea";
    },
    getCoreGroupLabel(chapter) {
      let label = "Data lancio core group";
      if(!chapter.coreGroupLaunch.actual) {
        label += " (previsional)";
      }
      return label;
    },
    getChapterLabel(chapter) {
      debugger;
      let label = "Data lancio capitolo";
      if(!chapter.chapterLaunch.actual) {
        label += " (previsional)";
      }
      return label;
    },
    fetchUsers() {
      let region = this.$state.getters["getRegion"];
      //todo
      //this.users = ApiServer.get("users?region=" + region);
    },
    setChapterLaunch(value) {
      let d = new Date(value);
      let today = new Date();
      //se sono in fase di creazione del capitolo e la data previsionale è minore della data attuale, quella data diventa automaticamente la data di lancio effettiva
      if (this.isCreating && d < today) {
        this.chapter.chapterLaunch.actual = value;
        this.chapter.chapterLaunchWarning = true;
      } else {
        this.chapter.chapterLaunch.prev = value;
      }
    },
    setCoreGroupLaunch(value) {
      let d = new Date(value);
      let today = new Date();

      //se sono in fase di creazione del capitolo e la data previsionale è minore della data attuale, quella data diventa automaticamente la data di lancio effettiva
      if (this.isCreating && d < today) {
        this.chapter.coreGroupLaunch.actual = value;
        this.chapter.coreGroupLaunchWarning = true;
      } else {
        this.chapter.coreGroupLaunch.prev = value;
      }
    },
    emitClose() {
      this.chapter = { ...chapterSkeleton };
      this.$emit("close");
    },
    saveChapter() {
      //todo
      ApiServer.post("chapter");
    },
    isCoreGroupOrChapter(item) {
      return (
        item.currentState === "CORE_GROUP" || item.currentState === "CHAPTER"
      );
    },
    isChapter(item) {
      return item.currentState === "CHAPTER";
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
          this.editMode = true;
          this.chapter = { ...this.editChapter };

        debugger;
          switch (this.chapter.currentState) {
            case "PROJECT":
              this.chapter["coreGroupLaunch"] = Utils.getMonthYear(
                this.chapter["coreGroupLaunch"]["prev"]
              );
              this.chapter["chapterLaunch"] = Utils.getMonthYear(
                this.chapter["chapterLaunch"]["prev"]
              );
              break;
            case "CORE_GROUP":
              this.chapter["coreGroupLaunch"] = Utils.getMonthYear(
                this.chapter["coreGroupLaunch"]["actual"]
              );
              this.chapter["chapterLaunch"] = Utils.getMonthYear(
                this.chapter["chapterLaunch"]["prev"]
              );
              break;
            case "CHAPTER":
              this.chapter["coreGroupLaunch"] = Utils.getMonthYear(
                this.chapter["coreGroupLaunch"]["actual"]
              );
              this.chapter["chapterLaunch"] = Utils.getMonthYear(
                this.chapter["chapterLaunch"]["actual"]
              );
              break;
          }
        } else {
          this.editMode = false;
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
