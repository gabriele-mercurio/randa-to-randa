<template>
  <v-container>
    <h3 class="py-4">
      Capitolo:
      <span class="font-italic font-weight-light mr-2">{{ chapter.name }}</span>
      Membri inizali:
      <span class="font-italic font-weight-light mr-2">3</span> Membri finali:
      <span class="font-italic font-weight-light">3</span>
    </h3>
    <Rana
      :rana="currentRana"
      :prevRana="prevRana"
      :ranaType="'renewedMembers'"
      :currentTimeslot="currentTimeslot"
      :editable="true"
    />

    <v-divider class="py-6"></v-divider>
    <div v-for="(rana, index) in ranas" :key="index" class="mt-4">

      <template v-if="index != 0 && rana && rana.timeslot !== 'T4' && isPastTimeslot(rana.timeslot) && ranas.length > 1">
        <Rana
          :rana.sync="rana"
          :ranaType="'renewedMembers'"
          :currentTimeslot="rana.timeslot"
          :editable="false"
        />
      </template>
    </div>
  </v-container>
</template>
<script>
import Utils from "../../services/Utils";
import ApiServer from "../../services/ApiServer";
import Rana from "../../components/Rana";
import RANAS from "../../mocks/ranas";

export default {
  components: {
    Rana
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
    async fetchChapter(chapterId) {
      if (!chapterId) {
        alert("Nessun capitolo specificato");
      }
      this.chapter = await ApiServer.get("chapter/" + chapterId);
      if (this.chapter.error) {
        alert("Errore nella get del capitolo");
      }
    },
    isPastTimeslot(timeslot) {
      return timeslot <= this.currentTimeslot;
    },
    async fetchRanas(chapterId) {
      this.ranas = await ApiServer.get(chapterId + "/rana");
      if(this.ranas.errorCode && this.ranas.errorCode == 404) {
        this.ranas = await ApiServer.post(chapterId + "/rana");
      }

      this.currentRana = this.ranas[0];
      if(this.ranas.length > 1) {
        this.prevRana = this.ranas[1];
      }
    }
  }
};
</script>
