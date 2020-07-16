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
          :appendMessage="true"
        />
        <MonthPicker
          :defLabel="getChapterLabel(chapter)"
          :defDate.sync="chapter.chapterLaunch"
          v-on:setdate="setChapterLaunch"
          :disabled="isChapter(chapter)"
          :invalidInterval="invalidInterval()"
          launchType="CHAPTER"
          :appendMessage="true"
        />

        <v-select
          v-if="!freeAccount"
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
          {{ $t("cancel") }}
        </v-btn>
        <v-btn
          type="submit"
          normal
          text
          color="primary"
          @click="saveChapter()"
          :disabled="!isFormValid()"
        >
          {{ $t("save") }}
        </v-btn>
      </div>
    </v-card-actions>

    <v-snackbar v-model="error" :timeout="timeout" top right>
      <v-icon color="primary">mdi-alert</v-icon>
      Errore nel salvataggio del capitolo
      <v-btn color="white" icon @click="error = false">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-snackbar>
  </v-card>
</template>
<script>
import Utils from "../services/Utils";
import ApiServer from "../services/ApiServer";
import MonthPicker from "../components/MonthPicker";

const mandatoryFields = ["name"];

const chapterSkeleton = {
  name: "",
  currentState: "PROJECT",
  chapterLaunch: null,
  coreGroupLaunch: null,
  coreGroupLaunchType: null,
  chapterLaunchType: null,
  actualLaunchChapterDate: null,
  prevLaunchChapterDate: null,
  actualLaunchCoregroupDate: null,
  prevLaunchCoregroupDate: null,
  director: null,
  members: 0
};

export default {
  components: {
    MonthPicker
  },
  data() {
    return {
      editMode: false,
      chapter: { ...chapterSkeleton },
      states: ["PROJECT", "CORE_GROUP", "CHAPTER"],
      coreGroupMessage: "",
      chapterMessage: "",
      error: false,
      timeout: 3000
    };
  },
  props: {
    editChapter: {
      type: Object,
      default: null
    },
    users: {
      type: Array,
      default: null
    },
    freeAccount: {
      type: Boolean,
      default: false
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
    addDay(date) {
      return date + "-01";
    },
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
    async saveChapter() {
      let data = {};
      data["name"] = this.chapter["name"];
      if(!this.freeAccount) {
        data["director"] = this.chapter["director"].id;
      } else {
        data["director"] = this.$store.getters["getUser"].id
      }
      if (this.chapter.coreGroupLaunchType === "actual") {
        data["actualLaunchCoregroupDate"] = this.addDay(
          this.chapter.coreGroupLaunch
        );
      } else {
        data["prevLaunchCoregroupDate"] = this.addDay(
          this.chapter.coreGroupLaunch
        );
      }
      if (this.chapter["chapterLaunchType"] === "actual") {
        data["actualLaunchChapterDate"] = this.addDay(
          this.chapter.chapterLaunch
        );
      } else {
        data["prevLaunchChapterDate"] = this.addDay(this.chapter.chapterLaunch);
      }
      try {
        let result;
        if (this.editMode) {
          result = await ApiServer.put("chapter/" + this.chapter.id, data);
        } else {
          let region = this.$store.getters["getRegion"];
          result = await ApiServer.post(region.id + "/chapter", data);
        }

        let updatedChapter = { ...this.chapter };

        let cgLaunch = {
          prev: null,
          actual: null
        };

        let cLaunch = {
          prev: null,
          actual: null
        };

        if (updatedChapter.coreGroupLaunchType === "actual") {
          cgLaunch.actual = updatedChapter.coreGroupLaunch;
        } else {
          cgLaunch.prev = updatedChapter.coreGroupLaunch;
        }

        if (updatedChapter.chapterLaunchType === "actual") {
          cLaunch.actual = updatedChapter.chapterLaunch;
        } else {
          cLaunch.prev = updatedChapter.chapterLaunch;
        }

        updatedChapter.coreGroupLaunch = cgLaunch;
        updatedChapter.chapterLaunch = cLaunch;

        if(!result.error) {
          updatedChapter.id = result.id;
          this.$emit("saveChapter", updatedChapter);
        }

      } catch (e) {
        this.error = true;
      }
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
