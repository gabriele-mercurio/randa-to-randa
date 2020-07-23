<template
  >
  <div>
    <v-data-table
      :headers="headers"
      :items="chapters"
      disable-pagination
      hide-default-footer
      :class="classSpec"
      class="mb-4"
      id="chaptersList"
    >
      <template v-slot:body>
        <tr v-for="item in chapters" :key="item.id" :class="item.currentState">
          <td class="small-font pl-2">{{ item.name }}</td>
          <td class="small-font pl-2">
            <div class="d-flex flex-column">
              <span :class="item.currentState">
                {{
                getChapterState(item.currentState)
                }}
              </span>
              <div v-if="item.currentState === 'SUSPENDED'">
                <small>(ripresa prevista {{item.resume.actual ? item.resume.actual : item.resume.prev}})</small>
              </div>
              <span class="font-italic font-weight-light">{{ getPrevResumeIfSuspended(item) }}</span>
            </div>
          </td>
          <td class="small-font pl-2">{{ item.director.fullName }}</td>
          <td class="small-font pl-2">{{ getChapterMembers(item) }}</td>
          <td class="small-font pl-2">
            <div class="d-flex flex-column justicy-center">
              <small class="font-italic font-weight-light" v-if="isPrev(item.coreGroupLaunch)">
                Previsional
                <v-tooltip right v-if="isExpired(item.coreGroupLaunch)">
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on" small class="primary--text">mdi-alert</v-icon>
                  </template>
                  <span>
                    La data prevista per il lancio del core group è stata
                    superata!
                  </span>
                </v-tooltip>
              </small>
              <span
                :class="{
                  'font-italic font-weight-light': isPrev(item.coreGroupLaunch)
                }"
              >{{ getPrevOrActualDate(item, 'CORE_GROUP')}}</span>
            </div>
          </td>
          <td class="small-font pl-2">
            <div class="d-flex flex-column justicy-center">
              <small class="font-italic font-weight-light" v-if="isPrev(item.chapterLaunch)">
                Previsional
                <v-tooltip right v-if="isExpired(item.chapterLaunch)">
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on" small class="primary--text">mdi-alert</v-icon>
                  </template>
                  <span>
                    La data prevista per il lancio del capitolo è stata
                    superata!
                  </span>
                </v-tooltip>
              </small>
              <span
                :class="{
                  'font-italic font-weight-light': isPrev(item.chapterLaunch)
                }"
              >{{ getPrevOrActualDate(item, "CHAPTER")}}</span>
            </div>
          </td>
          <td class="small-font pl-2">{{ getItemState(item) }}</td>
          <td class="actions">
            <v-menu bottom left>
              <template v-slot:activator="{ on }">
                <v-btn dark icon v-on="on">
                  <v-icon class="primary--text">mdi-dots-vertical</v-icon>
                </v-btn>
              </template>
              <v-list>
                <v-list-item @click="goToRana(item)" :disabled="item.currentState === 'SUSPENDED'">
                  <v-icon class="mr-1">mdi-text</v-icon>
                  <v-list-item-title v-if="iAmAssistant">
                    {{
                    $t("rana_proposal")
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                    getMenuLabel(item)
                    }}
                  </v-list-item-title>
                </v-list-item>

                <v-list-item @click="edit(item)" :disabled="item.currentState === 'SUSPENDED'">
                  <v-icon class="mr-1">mdi-pencil-outline</v-icon>
                  <v-list-item-title>Modifica capitolo</v-list-item-title>
                </v-list-item>

                <v-list-item
                  v-if="item.currentState == 'PROJECT'"
                  @click="showLaunchCoreGroupDateDialog(item)"
                  :class="{
                    disabled:
                      item.currentState === 'SUSPENDED' ||
                      item.currentState === 'CLOSED'
                  }"
                >
                  <v-list-item-title>
                    <v-icon>mdi-rocket-launch-outline</v-icon>Lancia core group
                  </v-list-item-title>
                </v-list-item>

                <v-list-item
                  v-if="item.currentState == 'CORE_GROUP'"
                  @click="showLaunchChapterDateDialog(item)"
                  :class="{
                    disabled:
                      item.currentState === 'SUSPENDED' ||
                      item.currentState === 'CLOSED'
                  }"
                >
                  <v-list-item-title>
                    <v-icon>mdi-rocket-launch-outline</v-icon>Lancia capitolo
                  </v-list-item-title>
                </v-list-item>

                <v-list-item @click="suspend(item)" v-if="canSuspend(item)">
                  <v-icon class="mr-1">mdi-stop-circle-outline</v-icon>
                  <v-list-item-title>Sospendi {{ getStateToLaunch(item) }}</v-list-item-title>
                </v-list-item>
                <v-list-item v-if="canClose(item)" @click="confirmResume(item)">
                  <v-icon class="mr-1">mdi-play-circle-outline</v-icon>
                  <v-list-item-title>Riprendi capitolo</v-list-item-title>
                </v-list-item>

                <v-list-item
                  @click="confirmClose(item)"
                  :class="{ disabled: item.currentStatus == 'CLOSED' }"
                >
                  <v-icon class="mr-1">mdi-close</v-icon>
                  <v-list-item-title>Chiudi {{ getStateToLaunch(item) }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </td>
        </tr>
      </template>
    </v-data-table>
    <Confirm :message="'Lanciare capitolo?'" :show.sync="showConfirmLaunchChapter" v-on:dialogResponse="launchChapter" />
    <Confirm :message="'Lanciare core group?'" :show.sync="showConfirmLaunchCoreGroup" v-on:dialogResponse="launchCoreGroup" />

    <Confirm :message="confirmMessage" :show.sync="showConfirmClose" v-on:dialogResponse="close" />
    <Confirm :message="confirmMessage" :show.sync="showConfirmResume" v-on:dialogResponse="resume" />

    <Snackbar
      :showSnackbar.sync="showSnackbar"
      :state.sync="snackbarState"
      :messageLabel.sync="snackbarMessageLabel"
    />

    <v-dialog v-model="suspendDialog" width="500">
      <ChapterSuspend v-on:close="closeSuspendDialog" :chapter.sync="currentChapter" />
    </v-dialog>

    <v-dialog v-model="launchChapterDateDialog" width="500" v-if="launchChapterDateDialog">
      <v-card>
        <v-card-title>Lancia capitolo</v-card-title>
        <v-card-text>
          <MonthPicker
            :defLabel="'Data lancio capitolo'"
            :defDate="getDefaultLaunchChapterDate()"
            v-on:setdate="setCurrentChapterLaunchDate"
            launchType="CHAPTER"
            :appendMessage="false"
            :invalidInterval="invalidChapterLaunchDate"
          />
        </v-card-text>
        <v-card-actions class="d-flex justify-end pa-3">
          <v-btn @click="launchChapterDateDialog = false; invalidChapterLaunchDate = false">Annulla</v-btn>
          <v-btn color="primary" :disabled="invalidChapterLaunchDate" @click="showConfirmLaunchChapter = true">Lancia</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="launchCoreGroupDateDialog" width="500" v-if="launchCoreGroupDateDialog">
      <v-card>
        <v-card-title>Lancia core group</v-card-title>
        <v-card-text>
          <MonthPicker
            :defLabel="'Data lancio core group'"
            :defDate="getDefaultLaunchCoreGroupDate()"
            v-on:setdate="setCurrentCoreGroupLaunchDate"
            launchType="CORE_GROUP"
            :appendMessage="false"
            :invalidInterval="invalidCoreGroupLaunchDate"
          />
        </v-card-text>
        <v-card-actions class="d-flex justify-end pa-3">
          <v-btn @click="launchCoreGroupDateDialog = false; invalidCoreGroupLaunchDate = false">Annulla</v-btn>
          <v-btn :disabled="invalidCoreGroupLaunchDate" color="primary" @click="showConfirmLaunchCoreGroup = true">Lancia</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-snackbar v-model="showLaunchedChapterSnackbar" :timeout="timeout" top right>
      <v-icon color="green">mdi-check</v-icon>
      Capitolo lanciato!
    </v-snackbar>
   <v-snackbar v-model="showLaunchedCoreGroupSnackbar" :timeout="timeout" top right>
      <v-icon color="green">mdi-check</v-icon>
      Core group lanciato!
    </v-snackbar>
      <v-snackbar v-model="showErrorLaunchedChapterSnackbar" :timeout="timeout" top right>
      <v-icon color="primary">mdi-alert</v-icon>
      Errore lancio capitolo...
    </v-snackbar>
   <v-snackbar v-model="showErrorLaunchedCoreGroupSnackbar" :timeout="timeout" top right>
      <v-icon color="primary">mdi-alert</v-icon>
      Errore lancio core group...
    </v-snackbar>
  </div>
</template>

<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import Snackbar from "../components/Snackbar";
import Confirm from "../components/Confirm";
import ChapterSuspend from "../components/ChapterSuspend";
import MonthPicker from "../components/MonthPicker";
export default {
  components: {
    Snackbar,
    ChapterSuspend,
    Confirm,
    MonthPicker,
  },
  data() {
    return {
      headers: [
        { align: "start", text: "Nome", value: "name" },
        { align: "start", text: "Stato", value: "currentState" },
        { align: "start", text: "Director", value: "director.fullName" },
        { align: "start", text: "Membri", value: "members" },
        { align: "start", text: "Core group", value: "coreGroupLaunch" },
        { align: "start", text: "Capitolo", value: "chapterLaunch" },
        { align: "start", text: "Stato", value: "state" },
        { align: "start", value: "actions" },
      ],
      shortFields: ["name", "director.fullName", "members", "currentState"],
      snackbarMessageLabel: "",
      snackbarColor: null,
      snackbarState: null,
      showSnackbar: false,
      suspendDialog: false,
      prevResumeDate: null,
      currentChapter: null,
      showConfirmLaunchChapter: false,
      showConfirmLaunchCoreGroup: false,
      showConfirmClose: false,
      showConfirmResume: false,
      launchChapterDateDialog: false,
      launchCoreGroupDateDialog: false,
      currentCoreGroupLaunchDate: null,
      currentChapterLaunchDate: null,
      showLaunchedCoreGroupSnackbar: false,
      showLaunchedChapterSnackbar: false,
      showErrorLaunchedCoreGroupSnackbar: false,
      showErrorLaunchedChapterSnackbar: false,
      invalidChapterLaunchDate: false,
      invalidCoreGroupLaunchDate: false,
      role: null,
      confirmMessage: "",
      timeout: 3000,
      currentItem: null,
      currentType: null,
    };
  },
  props: {
    classSpec: {
      type: String,
      default: "",
    },
    short: {
      type: Boolean,
      default: false,
    },
    chapters: {
      type: Array,
      default: null,
    },
    randa_info: {
      default: null,
    },
  },
  computed: {
    iAmAssistant() {
      if (this.$store.getters["getRegion"]) {
        return this.$store.getters["getRegion"].role === "ASSISTANT";
      }
    },
  },
  methods: {
    showLaunchChapterDateDialog(item) {
      this.currentItem = item;
      this.launchChapterDateDialog = true;
    },
    showLaunchCoreGroupDateDialog(item) {
      this.currentItem = item;
      this.launchCoreGroupDateDialog = true;
    },
    getDefaultLaunchCoreGroupDate() {
      return (
        Utils.getMonthYear(this.currentItem.coreGroupLaunch.prev) ??
        Utils.getMonthYear(new Date())
      );
    },
    setCurrentCoreGroupLaunchDate(date) {
      if(new Date(date) > new Date()) {
        this.invalidCoreGroupLaunchDate = "La data di lancio non può essere superiore alla data attuale";
      } else {
        this.invalidCoreGroupLaunchDate = null;
      }
      this.currentCoreGroupLaunchDate = date;
    },
    getDefaultLaunchChapterDate() {
      return (
        Utils.getMonthYear(this.currentItem.chapterLaunch.prev) ??
        Utils.getMonthYear(new Date())
      );
    },
    setCurrentChapterLaunchDate(date) {
      if(new Date(date) > new Date()) {
        this.invalidChapterLaunchDate = "La data di lancio non può essere superiore alla data attuale";
      } else {
        this.invalidChapterLaunchDate = null;
      }
      this.currentChapterLaunchDate = date;
    },

    getChapterMembers(item) {
      if (item.currentState !== "CLOSED") return item.members;
      return 0;
    },
    isExpired(item) {
      let today = new Date();
      today =
        today.getFullYear() +
        "-" +
        (today.getMonth() + 1).toString().padStart(2, "0");
      return item.prev && new Date(item.prev) < new Date(today);
    },
    canSuspend(item) {
      return (
        item.currentState == "CHAPTER" &&
        (this.role == "ADMIN" || this.role == "EXECUTIVE")
      );
    },
    canClose(item) {
      return (
        item.currentState == "SUSPENDED" &&
        (this.role == "ADMIN" || this.role == "EXECUTIVE")
      );
    },
    confirmClose(item) {
      this.showConfirmClose = true;
      this.confirmMessage = "Si sta per chiudere il capitolo. Proseguire?";
      this.currentItem = item;
    },
    confirmResume(item) {
      this.showConfirmResume = true;
      this.confirmMessage = "Si sta per riavviare il capitolo. Proseguire?";
      this.currentItem = item;
    },
    getMenuLabel(item) {
      this.role = this.$store.getters["getRegion"].role;
      switch (item.state) {
        case "APPR":
          return "Vai a rana...";
        case "PROP":
          if (this.role === "ADMIN" || this.role === "EXECUTIVE") {
            return "Approva rana...";
          } else {
            return "Vai a rana...";
          }
        case "TODO":
        case "REFUSED":
          if (this.role === "ADMIN" || this.role === "EXECUTIVE") {
            return "Approva rana...";
          } else {
            return "Proponi rana...";
          }
      }
    },
    //go to rana proposal or approve
    goToRana(item) {
      this.$router.push("/rana/" + item.id);
    },

    getChapterState(state) {
      return Utils.getChapterState(state);
    },

    getItemState(item) {
      if (item.currentState !== "CLOSED") {
        return Utils.getState(item.state);
      }
      return "";
    },

    closeSuspendDialog(isSuspended) {
      this.suspendDialog = false;
      if (isSuspended) {
        this.currentChapter.currentState = "SUSPENDED";
      }
    },

    edit(item) {
      this.$emit("edit", item);
    },

    getPrevResumeIfSuspended(item) {
      if (item.currentState === "SUSPENDED" && item.resume.prev) {
        return "Resume previsto: " + item.resume.prev;
      }
      return "";
    },

    async suspend(item) {
      this.suspendDialog = true;
      this.currentChapter = item;

      // let response = await ApiServer.put("api/" + "chapter/" + item.id + "/suspend");
      // this.showSnackbar = true;
      // if (!response.error) {
      //   this.snackbarState = "success";
      //   this.snackbarMessageLabel = "chapter_suspended";
      //   item.currentState = "SUSPENDED";
      // } else {
      //   this.snackbarState = "error";
      //   this.snackbarMessageLabel = "chapter_suspended_error";
      // }
    },

    async resume(item) {
      let response = await ApiServer.put(
        "api/" + "chapter/" + item.id + "/resume"
      );
      this.showSnackbar = true;
      if (!response.error) {
        this.snackbarState = "success";
        this.snackbarMessageLabel = "chapter_resumed";
        item.currentState = "CHAPTER";
      } else {
        this.snackbarState = "error";
        this.snackbarMessageLabel = "chapter_resumed_error";
      }
    },

    async close(item) {
      let response = await ApiServer.put(
        "api/" + "chapter/" + item.id + "/close"
      );
      if (!response.error) {
        item = response;
      }
      debugger;
    },

    confirmLaunch(item, type) {
      this.showConfirmLaunch = true;
      let label = type === "PROJECT" ? "core group" : "capitolo";
      debugger;
      this.confirmMessage = "Si sta per lanciare il " + label + ". Proseguire?";
      this.currentItem = item;
      this.currentType = type;
    },

    async launchCoreGroup(result) {
      this.showConfirmLaunch = false;
      this.launchCoreGroupDateDialog = false;
      if (result) {
        let response = await ApiServer.put(
          "api/" + "chapter/" + this.currentItem.id + "/launch-coregroup", {
            date: this.currentCoreGroupLaunchDate
          }
        );
        this.showSnackbar = true;
        if (!response.error) {
          this.showLaunchedCoreGroupSnackbar = true;
          this.$emit("updateChapters");
        } else {
          this.showErrorLaunchedCoreGroupSnackbar = true;
        }
      }
    },

    async launchChapter(result) {
      this.launchChapterDateDialog = false;
      this.showConfirmLaunch = false;
      if (result) {
        let response = await ApiServer.put(
          "api/" + "chapter/" + this.currentItem.id + "/launch", {
            date: this.currentChapterLaunchDate
          }
        );
        debugger;
        this.showSnackbar = true;
        if (!response.error) {
          this.showLaunchedChapterSnackbar = true;
          this.$emit("updateChapters");
        } else {
          this.showErrorLaunchedChapterSnackbar = true;
        }
      }
    },


    getPrevOrActualDate(item, step) {
      if (item.currentState === "CHAPTER") {
        if (step === "CORE_GROUP") {
          return Utils.getMonthYear(item.coreGroupLaunch.actual);
        } else {
          return Utils.getMonthYear(item.chapterLaunch.actual);
        }
      } else if (item.currentState === "CORE_GROUP") {
        if (step === "CORE_GROUP") {
          return Utils.getMonthYear(item.coreGroupLaunch.actual);
        } else {
          return Utils.getMonthYear(item.chapterLaunch.prev);
        }
      } else if (item.currentState === "PROJECT") {
        if (step === "CORE_GROUP") {
          return Utils.getMonthYear(item.coreGroupLaunch.prev);
        } else {
          return Utils.getMonthYear(item.chapterLaunch.prev);
        }
      } else {
        return "";
      }
    },

    isPrev(item) {
      return item.prev && !item.actual;
    },

    getStateToLaunch(item) {
      if (item.currentState === "PROJECT") {
        return "core group";
      } else if (item.currentState === "CORE_GROUP") {
        return "capitolo";
      }
    },
  },
  created() {
    setTimeout(() => {
      if (this.short) {
        this.headers = this.headers.filter((h) => {
          return this.shortFields.includes(h.value);
        });
      }
    });
  },

  watch: {
    chapters: {
      handler: function (old, n) {},
    },
  },
};
</script>
<style lang="scss">
#chaptersList {
  .hidden {
    display: none;
  }
  .disabled {
    pointer-events: none;
    opacity: 0.6;
  }
  th {
    background-color: lighten(lightgray, 15%);
    .v-icon {
      display: none !important;
    }
  }
  .small-font {
    font-size: 13px;
  }
  tr.CLOSED {
    pointer-events: none;
    opacity: 0.5;
  }
  tr.SUSPENDED {
    td:not(.actions) {
      pointer-events: none;
      opacity: 0.5;
    }
  }
}
</style>
