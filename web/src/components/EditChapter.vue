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
          :invalidInterval="invalidInterval()"
          launchType="CORE GROUP"
        />
        <MonthPicker
          :defLabel="getChapterLabel(chapter)"
          :defDate.sync="chapter.chapterLaunch"
          v-on:setdate="setChapterLaunch"
          :disabled="isChapter(chapter)"
          :invalidInterval="invalidInterval()"
          launchType="CHAPTER"
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

const mandatoryFields = ["name", "chapterLaunch", "coreGroupLaunch"];

const chapterSkeleton = {
  name: "",
  currentState: "PROJECT",
  chapterLaunch: null,
  coreGroupLaunch: null,
  coreGroupLaunchType: null,
  chapterLaunchType: null,
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
      states: ["PROJECT", "CORE_GROUP", "CHAPTER"],
      coreGroupMessage: "",
      chapterMessage: ""
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
      return label;
    },
    getChapterLabel(chapter) {
      let label = "Data lancio capitolo";
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

      if (!this.chapter.chapterLaunch) this.chapter.chapterLaunch = {};

      //se sono in fase di creazione del capitolo e la data previsionale è minore della data attuale, quella data diventa automaticamente la data di lancio effettiva
      this.chapter.chapterLaunch = value;
      if (this.isCreating && d < today) {
        this.chapter.chapterLaunchType = "actual";
      } else {
        this.chapter.chapterLaunchType = "prev";
      }
    },
    setCoreGroupLaunch(value) {
      let d = new Date(value);
      let today = new Date();
      if (!this.chapter.coreGroupLaunch) this.chapter.coreGroupLaunch = {};

      this.chapter.coreGroupLaunch = value;
      //se sono in fase di creazione del capitolo e la data previsionale è minore della data attuale, quella data diventa automaticamente la data di lancio effettiva
      if (this.isCreating && d < today) {
        this.chapter.coreGroupLaunchType = "actual";
      } else {
        this.chapter.coreGroupLaunchType = "prev";
      }
    },
    emitClose() {
      this.chapter = { ...chapterSkeleton };
      this.$emit("close");
    },
    saveChapter() {
      //todo
      this.chapter.director = "1425d188-e39f-4c4c-810e-d16c9d73e1f7";
      if (this.chapter.coreGroupLaunchType === "actual") {
        this.chapter.launchCoregroupDate = this.chapter.coreGroupLaunch.actual;
      } else {
        this.chapter.launchCoregroupDate = this.chapter.coreGroupLaunch.prev;
      }
      if (this.chapter.chapterLaunchType === "actual") {
        this.chapter.launchChapterDate = this.chapter.chapterLaunch.actual;
      } else {
        this.chapter.launchChapterDate = this.chapter.chapterLaunch.prev;
      }
      ApiServer.post(this.$store.getters["getRegion"].id + "/chapter", this.chapter);
    },
    isCoreGroupOrChapter(item) {
      return (
        item.currentState === "CORE_GROUP" || item.currentState === "CHAPTER"
      );
    },
    isChapter(item) {
      return item.currentState === "CHAPTER";
    },
    isFormValid() {
      for (let k of Object.keys(this.chapter)) {
        if (mandatoryFields.includes(k) && !this.chapter[k]) {
          return false;
        }
      }
      if (this.invalidInterval()) return false;
      return true;
    },
    invalidInterval() {
      if (this.chapter.coreGroupLaunch && this.chapter.chapterLaunch) {
        let cg_date = new Date(this.chapter.coreGroupLaunch);
        let c_date = new Date(this.chapter.chapterLaunch);
        if (cg_date <= c_date) return "";
        return "Attenzione, la data di lancio capitolo deve essere superiore a quella del lancio core group.";
      }
      return "";
    }
  },
  watch: {
    editChapter: {
      handler: function(newVal, oldVal) {
        if (this.editChapter) {
          this.editMode = true;
          this.chapter = { ...this.editChapter };

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
