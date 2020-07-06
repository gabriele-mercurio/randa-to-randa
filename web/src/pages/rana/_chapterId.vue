<template>
  <v-container>
    <v-row class="pa-0 ma-0">
      <v-col cols="1" class="pa-0 ma-0 d-flex justify-center align-center">
        <v-btn small fab color="primary" @click="goToPrevChapter()">
          <v-icon>mdi-arrow-left</v-icon>
        </v-btn>
      </v-col>
      <v-col cols="10 d-flex justify-center align-center">
        <h3 class="py-4">
          Capitolo:
          <span class="font-italic font-weight-light mr-2">{{
            chapter.name
          }}</span>
          <span class="font-italic font-weight-light mr-2">{{
            getChapterState(chapter.currentState)
          }}</span>

          <div v-if="chapter_stats">
            Approvati:
            <span class="font-italic font-weight-light mr-2">{{
              chapter_stats["approved"].length
            }}</span>
            Proposte:
            <span class="font-italic font-weight-light mr-2">{{
              chapter_stats["proposed"].length + chapter_stats["todo"].length
            }}</span>
           
          </div>

          <!-- Membri
          finali:
          <span class="font-italic font-weight-light">3</span> -->
        </h3>
      </v-col>
      <v-col cols="1" class="pa-0 ma-0 d-flex justify-center align-center">
        <v-btn fab small color="primary" @click="goToNextChapter()">
          <v-icon>mdi-arrow-right</v-icon>
        </v-btn>
      </v-col>
    </v-row>
    <v-divider></v-divider>
    <Rana
      :rana="currentRana"
      :prevRana="prevRana"
      :ranaType="'renewedMembers'"
      :editable="true"
      v-on:updateRanas="updateRanas"
      v-if="currentRana && currentRana.randa_timeslot == currentRana.timeslot"
    />

    <div v-for="(rana, index) in ranas" :key="index" class="mt-4">
      <template
        v-if="
          index != 0 &&
            rana &&
            rana.timeslot !== 'T4' &&
            isPastTimeslot(rana.timeslot) &&
            ranas.length > 1
        "
      >
        <Rana
          :rana.sync="rana"
          v-on:fetchRana="fetchRanas(null)"
          :ranaType="'renewedMembers'"
          v-on:updateRanas="updateRanas"
          :editable="true"
        />
      </template>

      <EditChapter
        v-if="isFreeAccount()"
        :show="showEditChapter"
        :editChapter.sync="editChapter"
        :users="users"
        v-on:close="showEditChapter = false"
        v-on:saveChapter="updateChapters"
      />
    </div>

    <div
      v-if="chapter_stats && chapter_stats.all_approved"
      class="d-flex justify-end mt-5"
    >
      <v-btn @click="goToRandaRevised()"
        >Vai a randa revised {{ chapter_stats.randa_timeslot }}</v-btn
      >
    </div>
  </v-container>
</template>
<script>
import Utils from "../../services/Utils";
import ApiServer from "../../services/ApiServer";
import Rana from "../../components/Rana";
import EditChapter from "../../components/EditChapter";
import RANAS from "../../mocks/ranas";

export default {
  components: {
    Rana,
    EditChapter
  },
  data() {
    return {
      chapter: {},
      currentTimeslot: null,
      ranas: null,
      currentRana: null,
      prevRana: null,
      chapter_stats: null
    };
  },
  created() {
    this.ranas = RANAS;

    let numericTimeslot = Utils.getNumericTimeslot();
    this.currentTimeslot = Utils.getCurrentTimeslot();

    // this.currentRana = JSON.parse(
    //   JSON.stringify(this.ranas[this.currentTimeslot])
    // );

    setTimeout(() => {
      let chapterId = this.$route.params.chapterId || false;

      this.fetchChapter(chapterId);
      this.fetchRanas(chapterId);
    });
  },
  methods: {
    updateRanas(ranas) {
      this.fetchRanas();
      this.fetchChaptersStatistics();
      this.currentRana = this.ranas[0];
      if (this.ranas.length > 1) {
        this.prevRana = this.ranas[1];
      }
    },

    getChapterState(state) {
      return Utils.getChapterState(state);
    },

    goToRandaRevised() {
      this.$router.push("/randa/randa-revised");
    },

    isPrevApproved(rana) {
      let index;
      let r = this.ranas.find((r, i) => {
        index = i;
        return rana.id === r.id;
      });
      return this.ranas[index + 1]
        ? this.ranas[index + 1].state === "APPR"
          ? true
          : false
        : true;
    },
    isNotApproved(rana) {
      rana.state !== "APPR";
    },
    isFreeAccount() {
      return false;
    },
    goToPrevchapter() {},

    goToNextChapter() {
      let index = 0;
      for (let i = 0; i < this.$store.getters["getChapters"].length; i++) {
        if (this.$store.getters["getChapters"][i].id == this.chapter.id) {
          if (i < this.$store.getters["getChapters"].length + 1) {
            this.$router.push(this.$store.getters["getChapters"][i + 1].id);
          }
        }
      }
    },
    goToPrevChapter() {
      let index = 0;
      for (let i = 0; i < this.$store.getters["getChapters"].length; i++) {
        if (this.$store.getters["getChapters"][i].id == this.chapter.id) {
          if (i >= 0) {
            this.$router.push(this.$store.getters["getChapters"][i - 1].id);
          }
        }
      }
    },
    async fetchChapter(chapterId) {
      if (!chapterId) {
        alert("Nessun capitolo specificato");
      }
      this.chapter = await ApiServer.get("chapter/" + chapterId);
    },
    async fetchChaptersStatistics() {
      let region_id = this.$store.getters["getRegion"].id;
      this.chapter_stats = await ApiServer.get(
        region_id + "/chapters-statistics"
      );
    },
    isPastTimeslot(timeslot) {
      return timeslot <= this.currentTimeslot;
    },
    async fetchRanas(chapterId) {
      if (!chapterId) {
        chapterId = this.$route.params.chapterId || false;
      }
      debugger;
      this.ranas = await ApiServer.get(chapterId + "/rana");
      if (this.ranas.errorCode && this.ranas.errorCode == 404) {
        this.ranas = await ApiServer.post(chapterId + "/rana");
        this.fetchChaptersStatistics();
      } else {
        this.fetchChaptersStatistics();
      }

      this.currentRana = this.ranas[0];
      if (this.ranas.length > 1) {
        this.prevRana = this.ranas[1];
      }
    }
  }
};
</script>
