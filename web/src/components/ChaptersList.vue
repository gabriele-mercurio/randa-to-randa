<template>
  <v-data-table
    :headers="headers"
    :items="chapters"
    disable-pagination
    class="elevation-3"
  >
    <template v-slot:item.currentState="{ item }">
      <span :class="item.currentState">{{ item.currentState }}</span>
    </template>
    <template v-slot:item.currentState="{ item }">
      <span :class="item.currentState">{{ item.currentState }}</span>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-menu bottom left>
        <template v-slot:activator="{ on }">
          <v-btn dark icon v-on="on">
            <v-icon class="primary--text">mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item @click="edit(item)">
            <v-list-item-title>Modifica capitolo</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </template>
    <template v-slot:item.coreGroupLaunch="{ item }">
      <div class="d-flex flex-column justicy-center">
        <small
          class="font-italic font-weight-light"
          v-if="isPrev(item.coreGroupLaunch)"
          >Previsional >

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

    <template v-slot:item.chapterLaunch="{ item }">
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
    </template>
  </v-data-table>
</template>
<script>

import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
export default {
  components:{
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
      chapters: []
    };
  },
  methods: {
    edit() {
      this.$emit("edit", item);
    },
    async fetchChapters() {
      if (this.$store.getters["getRegion"]) {
        let role = this.$store.getters["getRole"]
          ? "?role=" + this.$store.getters["getRole"]
          : "";
        this.chapters = await ApiServer.get(
          this.$store.getters["getRegion"].id + "/chapters" + role
        );
      }

      // console.log(this.chapters);
      // this.chapters = await Promise.resolve([
      //   {
      //     chapterLaunch: {
      //       prev: "2008-03",
      //       actual: null
      //     },
      //     closureDate: "string",
      //     coreGroupLaunch: {
      //       prev: "2020-09",
      //       actual: "2020-12"
      //     },
      //     currentState: "PROJECT",
      //     director: {
      //       id: 0,
      //       fullName: "Luigi luigetti"
      //     },
      //     id: 0,
      //     members: 10,
      //     name: "Abn",
      //     suspDate: null,
      //     warning: "CHAPTER"
      //   },
      //   {
      //     chapterLaunch: {
      //       prev: "2018-04",
      //       actual: "2020-05"
      //     },
      //     closureDate: null,
      //     coreGroupLaunch: {
      //       prev: "2020-08",
      //       actual: "2020-07"
      //     },
      //     currentState: "PROJECT",
      //     director: {
      //       id: 0,
      //       fullName: "Luigi poi"
      //     },
      //     id: 1,
      //     members: 100,
      //     name: "Saracap",
      //     suspDate: null,
      //     warning: null
      //   }
      // ]);
    },
    getPrevOrActualDate(item) {
      if (item.actual != null) {
        return Utils.getMonthYear(item.actual);
      } else {
        return Utils.getMonthYear(item.prev);
      }
    },

    isPrev(item) {
      return item.actual == null;
    }
  },
  created() {
    this.fetchChapters();
  }
};
</script>