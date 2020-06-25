<template>
  <v-data-table
    :headers="headers"
    :items="chapters"
    disable-pagination
    hide-default-footer
    :class="classSpec"
  >
    <template v-slot:item.currentState="{ item }">
      <div class="d-flex flex-column">
        <span :class="item.currentState">{{ item.currentState }}</span>
        <span class="font-italic font-weight-light">
          {{ getPrevResumeIfSuspended(item) }}
        </span>
      </div>
    </template>
    <template v-slot:item.actions="{ item }" v-if="!short">
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
              $t("approve_rana")
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
            @click="launch(item, item.currentState)"
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
          <v-list-item
            @click="suspend(item)"
            v-if="item.currentState == 'CHAPTER'"
          >
            <v-icon class="mr-1">mdi-stop-circle-outline</v-icon>
            <v-list-item-title
              >Sospendi {{ getStateToLaunch(item) }}</v-list-item-title
            >
          </v-list-item>
          <v-list-item
            v-if="item.currentState == 'SUSPENDED'"
            @click="resume(item)"
          >
            <v-icon class="mr-1">mdi-play-circle-outline</v-icon>
            <v-list-item-title>Riprendi capitolo</v-list-item-title>
          </v-list-item>

          <v-list-item
            @click="close(item)"
            :class="{ disabled: item.currentStatus == 'CLOSED' }"
          >
            <v-icon class="mr-1">mdi-close</v-icon>
            <v-list-item-title
              >Chiudi {{ getStateToLaunch(item) }}</v-list-item-title
            >
          </v-list-item>
        </v-list>
      </v-menu>
    </template>
    <template v-slot:item.coreGroupLaunch="{ item }" v-if="!short">
      <div class="d-flex flex-column justicy-center">
        <small
          class="font-italic font-weight-light"
          v-if="isPrev(item.coreGroupLaunch)"
          >Previsional

          <v-tooltip right v-if="item.warning == 'COREGROUP'">
            <template v-slot:activator="{ on }">
              <v-icon v-on="on" small class="primary--text">mdi-alert</v-icon>
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
    </template>

    <template v-slot:item.chapterLaunch="{ item }" v-if="!short">
      <div class="d-flex flex-column justicy-center">
        <small
          class="font-italic font-weight-light"
          v-if="isPrev(item.chapterLaunch)"
          >Previsional
          <v-tooltip right v-if="item.warning == 'CHAPTER'">
            <template v-slot:activator="{ on }">
              <v-icon v-on="on" small class="primary--text">mdi-alert</v-icon>
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
    </template>
  </v-data-table>
</template>
<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import Snackbar from "../components/Snackbar";
import ChapterSuspend from "../components/ChapterSuspend";
export default {
  components: {
    Snackbar,
    ChapterSuspend
  },
  data() {
    return {
      headers: [
        { text: "Nome", value: "name" },
        { text: "Stato", value: "currentState" },
        { text: "Direttore", value: "director.fullName" },
        { text: "Membri", value: "members" },
        { text: "Core group", value: "coreGroupLaunch" },
        { text: "Capitolo", value: "chapterLaunch" },
        { value: "actions" }
      ],
      shortFields: ["name", "director.fullName", "members", "currentState"],
      snackbarMessageLabel: "",
      snackbarColor: null,
      snackbarState: null,
      showSnackbar: false,
      suspendDialog: false,
      prevResumeDate: null,
      currentChapter: null
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
    }
  },
  computed: {
    iAmAssistant() {
      if (this.$store.getters["getRegion"]) {
        return this.$store.getters["getRegion"].role === "ASSISTANT"
      }
    }
  },
  methods: {
    //go to rana proposal or approve
    goToRana(item) {
      this.$router.push("rana/" + item.id);
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

    async launch(item, type) {
      let action = type === "CORE_GROUP" ? "launch" : "launch-coregroup";
      let response = await ApiServer.put("chapter/" + item.id + "/" + action);
      this.showSnackbar = true;
      if (!response.error) {
        this.snackbarState = "success";
        this.snackbarMessageLabel =
          type === "PROJECT" ? "core_group_launched" : "chapter_launched";
        item.currentState = type === "PROJECT" ? "CORE_GROUP" : "CHAPTER";
      } else {
        this.snackbarState = "error";
        this.snackbarMessageLabel =
          type === "PROJECT"
            ? "core_group_launch_error"
            : "chapter_launch_error";
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
    if (this.short) {
      this.headers = this.headers.filter(h => {
        return this.shortFields.includes(h.value);
      });
    }
  },

  watch: {
    chapters: {
      handler: function(old, n) {}
    }
  }
};
</script>
<style>
.hidden {
  display: none;
}
.disabled {
  pointer-events: none;
  opacity: 0.6;
}
</style>
