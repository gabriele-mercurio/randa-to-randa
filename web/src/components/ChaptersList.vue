<template
  ><div>
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
              <span :class="item.currentState">{{
                getChapterState(item.currentState)
              }}</span>
              <span class="font-italic font-weight-light">
                {{ getPrevResumeIfSuspended(item) }}
              </span>
            </div>
          </td>
          <td class="small-font pl-2">{{ item.director.fullName }}</td>
          <td class="small-font pl-2">{{ item.members }}</td>
          <td class="small-font pl-2">
            <div class="d-flex flex-column justicy-center">
              <small
                class="font-italic font-weight-light"
                v-if="isPrev(item.coreGroupLaunch)"
                >Previsional

                <v-tooltip right v-if="isExpired(item.coreGroupLaunch)">
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on" small class="primary--text"
                      >mdi-alert</v-icon
                    >
                  </template>
                  <span
                    >La data prevista per il lancio del core group è stata
                    superata!</span
                  >
                </v-tooltip>
              </small>
              <span
                :class="{
                  'font-italic font-weight-light': isPrev(item.coreGroupLaunch)
                }"
                >{{ getPrevOrActualDate(item.coreGroupLaunch) }}</span
              >
            </div>
          </td>
          <td class="small-font pl-2">
            <div class="d-flex flex-column justicy-center">
              <small
                class="font-italic font-weight-light"
                v-if="isPrev(item.chapterLaunch)"
                >Previsional
                <v-tooltip right v-if="isExpired(item.chapterLaunch)">
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on" small class="primary--text"
                      >mdi-alert</v-icon
                    >
                  </template>
                  <span
                    >La data prevista per il lancio del capitolo è stata
                    superata!</span
                  >
                </v-tooltip>
              </small>
              <span
                :class="{
                  'font-italic font-weight-light': isPrev(item.chapterLaunch)
                }"
                >{{ getPrevOrActualDate(item.chapterLaunch) }}</span
              >
            </div>
          </td>
          <td class="small-font pl-2">{{ getItemState(item) }}</td>
          <td>
            <v-menu bottom left>
              <template v-slot:activator="{ on }">
                <v-btn dark icon v-on="on">
                  <v-icon class="primary--text">mdi-dots-vertical</v-icon>
                </v-btn>
              </template>
              <v-list>
                <v-list-item @click="goToRana(item)">
                  <v-icon class="mr-1">mdi-text</v-icon>
                  <v-list-item-title v-if="iAmAssistant">{{
                    $t("rana_proposal")
                  }}</v-list-item-title>
                  <v-list-item-title v-else>{{
                    getMenuLabel(item)
                  }}</v-list-item-title>
                </v-list-item>

                <v-list-item @click="edit(item)">
                  <v-icon class="mr-1">mdi-pencil-outline</v-icon>
                  <v-list-item-title>Modifica capitolo</v-list-item-title>
                </v-list-item>
                <v-list-item
                  v-if="
                    item.currentState == 'PROJECT' ||
                      item.currentState == 'CORE_GROUP'
                  "
                  @click="confirmLaunch(item, item.currentState)"
                  :class="{
                    disabled:
                      item.currentState === 'SUSPENDED' ||
                      item.currentState === 'CLOSED'
                  }"
                >
                  <v-list-item-title>
                    <v-icon>mdi-rocket-launch-outline</v-icon>
                    Lancia {{ getStateToLaunch(item) }}
                  </v-list-item-title>
                </v-list-item>
                <v-list-item @click="suspend(item)" v-if="canSuspend(item)">
                  <v-icon class="mr-1">mdi-stop-circle-outline</v-icon>
                  <v-list-item-title
                    >Sospendi {{ getStateToLaunch(item) }}</v-list-item-title
                  >
                </v-list-item>
                <v-list-item v-if="canClose(item)" @click="resume(item)">
                  <v-icon class="mr-1">mdi-play-circle-outline</v-icon>
                  <v-list-item-title>Riprendi capitolo</v-list-item-title>
                </v-list-item>

                <v-list-item
                  @click="confirmClose(item)"
                  :class="{ disabled: item.currentStatus == 'CLOSED' }"
                >
                  <v-icon class="mr-1">mdi-close</v-icon>
                  <v-list-item-title
                    >Chiudi {{ getStateToLaunch(item) }}</v-list-item-title
                  >
                </v-list-item>
              </v-list>
            </v-menu>
          </td>
        </tr>
      </template>
    </v-data-table>
    <Confirm
      :message="confirmMessage"
      :show.sync="showConfirmLaunch"
      v-on:dialogResponse="launch"
    />

    <Confirm
      :message="confirmMessage"
      :show.sync="showConfirmClose"
      v-on:dialogResponse="close"
    />

    <Snackbar
      :showSnackbar.sync="showSnackbar"
      :state.sync="snackbarState"
      :messageLabel.sync="snackbarMessageLabel"
    />

    <v-dialog v-model="suspendDialog" width="500">
      <ChapterSuspend
        v-on:close="closeSuspendDialog"
        :chapter.sync="currentChapter"
      />
    </v-dialog>
  </div>
</template>

<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import Snackbar from "../components/Snackbar";
import Confirm from "../components/Confirm";
import ChapterSuspend from "../components/ChapterSuspend";
export default {
  components: {
    Snackbar,
    ChapterSuspend,
    Confirm
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
        { align: "start", value: "actions" }
      ],
      shortFields: ["name", "director.fullName", "members", "currentState"],
      snackbarMessageLabel: "",
      snackbarColor: null,
      snackbarState: null,
      showSnackbar: false,
      suspendDialog: false,
      prevResumeDate: null,
      currentChapter: null,
      showConfirmLaunch: false,
      showConfirmClose: false,
      confirmMessage: null,
      role: null,
      currentItem: null,
      currentType: null
    };
  },
  props: {
    classSpec: {
      type: String,
      default: ""
    },
    short: {
      type: Boolean,
      default: false
    },
    chapters: {
      type: Array,
      default: null
    },
    randa_info: {
      default: null
    }
  },
  computed: {
    iAmAssistant() {
      if (this.$store.getters["getRegion"]) {
        return this.$store.getters["getRegion"].role === "ASSISTANT";
      }
    }
  },
  methods: {
    close() {

    },
    isExpired(item) {
      let today = new Date();
      today = today.getFullYear() + "-" + (today.getMonth() + 1);
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
      return Utils.getState(item.state);
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

      // let response = await ApiServer.put("chapter/" + item.id + "/suspend");
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
      let response = await ApiServer.put("chapter/" + item.id + "/resume");
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

    confirmLaunch(item, type) {
      debugger;
      this.showConfirmLaunch = true;
      let label = type === "CORE_GROUP" ? "core group" : "capitolo";
      this.confirmMessage = "Si sta per lanciare il " + label + ". Proseguire?";
      this.currentItem = item;
      this.currentType = type;
    },

    async launch(result) {
      debugger;
      this.showConfirmLaunch = false;
      if (result) {
        let action =
          this.currentType === "CORE_GROUP" ? "launch" : "launch-coregroup";
        let response = await ApiServer.put(
          "chapter/" + this.currentItem.id + "/" + action
        );
        this.showSnackbar = true;
        if (!response.error) {
          this.snackbarState = "success";
          this.snackbarMessageLabel =
            this.currentType === "PROJECT"
              ? "core_group_launched"
              : "chapter_launched";
          this.currentItem.currentState =
            type === "PROJECT" ? "CORE_GROUP" : "CHAPTER";
        } else {
          this.snackbarState = "error";
          this.snackbarMessageLabel =
            this.currentType === "PROJECT"
              ? "core_group_launch_error"
              : "chapter_launch_error";
        }
      }
    },

    getPrevOrActualDate(item) {
      if (item.actual != null) {
        return Utils.getMonthYear(item.actual);
      } else if (item.prev) {
        return Utils.getMonthYear(item.prev);
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
    }
  },
  created() {
    setTimeout(() => {
      if (this.short) {
        this.headers = this.headers.filter(h => {
          return this.shortFields.includes(h.value);
        });
      }
    });
  },

  watch: {
    chapters: {
      handler: function(old, n) {}
    }
  }
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
}
</style>
