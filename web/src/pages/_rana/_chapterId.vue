<template>
  <v-container>
    <h3 class="py-4">
      Capitolo:
      <span class="font-italic font-weight-light">{{ chapter.name }}</span>
    </h3>
    <h3 class="pb-4">
      Membri inizali:
      <span class="font-italic font-weight-light">3</span> Membri finali:
      <span class="font-italic font-weight-light">3</span>
    </h3>
    <Rana
      :rana="currentRana"
      :ranaType="'renewedMembers'"
      :currentTimeslot="currentTimeslot"
      :editable="true"
    />

    <v-divider class="py-6"></v-divider>
    <div v-for="(rana, timeslot) in ranas" :key="timeslot">
      <template v-if="timeslot !== 'T4' && isPastTimeslot(timeslot)">
        <Rana
          :rana="rana"
          :ranaType="'renewedMembers'"
          :currentTimeslot="timeslot"
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
      lastTimeslot: null,
      ranas: null,
      currentRana: null
    };
  },
  created() {
    this.ranas = RANAS;
    let numericTimeslot = Utils.getNumericTimeslot();

    this.currentTimeslot = Utils.getCurrentTimeslot();
    this.lastTimeslot = "T" + (numericTimeslot - 1);

    this.currentRana = this.ranas[this.lastTimeslot];

    setTimeout(() => {
      let chapterId = this.$route.params.chapterId || false;
      this.fetchChapter(chapterId);
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
      //this.rana = ApiServer.get("rana");
    }
  }
};
</script>
