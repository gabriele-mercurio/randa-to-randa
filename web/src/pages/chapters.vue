<template>
  <div class="ma-4 fill-height">
    <template>
      <div v-if="randa_info" class="my-3">
        <span
          ><span class="font-weight-black">Randa</span>:
          {{ randa_info.timeslot }} {{ randa_info.year }}</span
        >
        <div v-if="randa_info.state === 'TODO' && allChaptersApproved()">
          <v-btn color="primary" @click="goToRanda()">
            Apporavazione randa
          </v-btn>
        </div>
          <div v-if="randa_info.state === 'APPR'">
          <v-btn color="primary" @click="createNextTimeslot()">
            Avvia compilazione randa {{getNextTimeslotLabel()}}
          </v-btn>
        </div>
        <span v-else class="font-italic font-weight-light"
          >({{ getState(randa_info.state) }})
        </span>
      </div>
      <div v-if="randa_info && randa_info.refuse_note && (role == 'ADMIN' || role == 'EXECUTIVE')">
        <v-icon small class="primary--text">mdi-alert</v-icon>
        Nota BNI:
        {{ randa_info.refuse_note }}
      </div>
      <ChaptersList
        :chapters.sync="chapters"
        :randa_info.sync="randa_info"
        :classSpec="'elevation-3'"
        v-on:edit="openEditModal"
        v-if="!noChaptersFound"
      />

      <div v-else>
        <NoData :message="'Nessun capitolo trovato'" />
        Nessun capitolo trovato :(
      </div>
    </template>

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
        :freeAccount.sync="freeAccount"
        v-on:close="showEditChapter = false"
        v-on:saveChapter="updateChapters"
      />
    </v-dialog>

    <v-tooltip bottom>
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          fixed
          fab
          bottom
          right
          color="primary"
          v-bind="attrs"
          v-on="on"
          @click="newChapter()"
          alt="Nuovo capitolo"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </template>
      <span>Nuovo capitolo</span>
    </v-tooltip>
  </div>
</template>

<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import EditChapter from "../components/EditChapter";
import ChaptersList from "../components/ChaptersList";
import NoData from "../components/NoData";

export default {
  data() {
    return {
      showEditChapter: false,
      editChapter: null,
      chapters: [],
      users: [],
      regionId: null,
      noChaptersFound: false,
      freeAccount: false,
      randa_info: null,
      role: null
    };
  },
  components: {
    EditChapter,
    ChaptersList,
    NoData
  },
  methods: {
    openEditModal(chapter) {
      this.editChapter = chapter;
      this.showEditChapter = true;
    },

    async createNextTimeslot() {
      let response = await ApiServer.put(this.regionId + "/create-next-timeslot");
      location.reload();
    },

    getNextTimeslotLabel() {
      let t = this.randa_info.timeslot;
      return "T" + (t.substr(-1) * 1 + 1);
    },

    goToRanda() {
      this.$router.push("/randa/randa-revised");
    },

    getState(randa_state) {
      if (randa_state == "TODO") {
        if (!this.allChaptersApproved()) {
          return "Approvare tutti i rana";
        } else {
          return "Approvare randa";
        }
      } else {
        return Utils.getRandaState(randa_state);
      }
    },

    allChaptersApproved() {
      let all_approved = true;
      this.chapters.forEach(c => {
        if (c.state != "APPR") {
          all_approved = false;
        }
      });
      return all_approved;
    },

    updateChapters(chapter) {
      this.showEditChapter = false;
      this.chapters.push(chapter);
      if (this.freeAccount) {
        this.$router.push("/rana/" + chapter.id);
      }
    },

    newChapter() {
      this.editChapter = null;
      this.showEditChapter = true;
    },

    async fetchUsersPerRegion() {
      this.users = await ApiServer.get(this.regionId + "/users");
    },

    async fetchChapters() {
      let response = await ApiServer.get(this.regionId + "/chapters");
      if (response.errorCode === 404) {
        this.noChaptersFound = true;
      } else if (response.errorCode === "403") {
      } else {
        this.chapters = response.chapters;
        this.randa_info = response.randa;
        this.$store.commit("setChapters", this.chapters);
      }
    }
  },
  created() {
    setTimeout(async () => {
      this.regionId = this.$store.getters["getRegion"].id;
      let role = this.$store.getters["getRegion"].role;
      this.role = role;
      if (role !== "NATIONAL") {
        this.fetchChapters();
        this.fetchUsersPerRegion();
      }
    });
  }
};
</script>
