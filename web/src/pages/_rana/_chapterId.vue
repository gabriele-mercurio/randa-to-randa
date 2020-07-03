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
          Membri inizali:
          <span class="font-italic font-weight-light mr-2">3</span>
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
      v-if="
        currentRana &&
          currentRana.timeslot !== 'T4' &&
          isPrevApproved(currentRana)
      "
    />

    <v-divider class="py-6"></v-divider>
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
        <Rana :rana.sync="rana" :ranaType="'renewedMembers'" :editable="true" />
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
      prevRana: null
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
      this.ranas = ranas;
      this.currentRana = this.ranas[0];
      if (this.ranas.length > 1) {
        this.prevRana = this.ranas[1];
      }
    },

    isPrevApproved(rana) {

      let index;
      let r = this.ranas.find((r, i) => {
        index = i;
        return rana.id === r.id;
      });
      return this.ranas[index+1] ? (this.ranas[index+1].state === "APPROVED" ? true : false) : true;
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
    async fetchChapter(chapterId) {
      if (!chapterId) {
        alert("Nessun capitolo specificato");
      }
      this.chapter = await ApiServer.get("chapter/" + chapterId);
      
    },
    isPastTimeslot(timeslot) {
      return timeslot <= this.currentTimeslot;
    },
    async fetchRanas(chapterId) {
      this.ranas = await ApiServer.get(chapterId + "/rana");
      if (this.ranas.errorCode && this.ranas.errorCode == 404) {
        this.ranas = await ApiServer.post(chapterId + "/rana");
      }

      this.currentRana = this.ranas[0];
      if (this.ranas.length > 1) {
        this.prevRana = this.ranas[1];
      }
    }
  }
};
</script>
