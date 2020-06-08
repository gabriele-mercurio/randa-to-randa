<template>
  <v-data-table
    :headers="headers"
    :items="chapters"
    disable-pagination
    hide-default-footer
    :class="classSpec"
  >
    <template v-slot:item.currentState="{ item }">
      <span :class="item.currentState">{{ item.currentState }}</span>
    </template>
    <template v-slot:item.actions="{ item }" v-if="!short">
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
          <v-list-item @click="launch(item)" class="{'disabled': item.currentState === 'SUSPENDED' || item.currentState === 'CLOSED'}">
            <v-list-item-title>Lancia {{getStateToLaunch()}} </v-list-item-title>
          </v-list-item>
          <v-list-item @click="suspend(item)">
            <v-list-item-title>Sospendi capitolo</v-list-item-title>
          </v-list-item>
          <v-list-item @click="resume(item)" class="{'disabled': item.currentStatus !== 'SUSPENDED'}">
            <v-list-item-title>Riprendi capitolo</v-list-item-title>
          </v-list-item>
          <v-list-item @click="stimateResume(item)" class="{'disabled': item.currentStatus !== 'SUSPENDED'}">
            <v-list-item-title>Stima ripresa capitolo</v-list-item-title>
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
    </template>
  </v-data-table>
</template>
<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
export default {
  components: {},
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
      shortFields: ["name", "director.fullName", "members", "currentState"]
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
  methods: {
    edit(item) {
      this.$emit("edit", item);
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
      if(item.currentState === "PROJECT") {
        return "core group";
      } else if(item.currentState === "CORE_GROUP") {
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
</style>
