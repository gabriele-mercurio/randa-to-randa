<template>
  <div>
    <v-card>
      <v-card-title>Lancia {{chapterState}}</v-card-title>
      <v-card-text>
        <MonthPicker
          :defLabel="'Data lancio ' + chapterState"
          :defDate="currentLaunchDate"
          v-on:setdate="setCurrentLaunchDate"
          :launchType="chapterState == 'capitolo' ? 'CHAPTER' : 'CORE_GROUP'"
          :appendMessage="false"
          :invalidInterval="invalidLaunchDate"
        />
      </v-card-text>
      <v-card-actions class="d-flex justify-end pa-3">
        <v-btn @click="close()">Annulla</v-btn>
        <v-btn
          color="primary"
          :disabled="invalidLaunchDate"
          @click="showConfirmLaunch = true"
        >Lancia</v-btn>
      </v-card-actions>
    </v-card>
    <Confirm
      :message="'Lanciare ' + chapterState + '?'"
      :show.sync="showConfirmLaunch"
      v-on:dialogResponse="launch"
    />
  </div>
</template>
<script>
import Utils from "../services/Utils";
import MonthPicker from "../components/MonthPicker";
import Confirm from "../components/Confirm";
export default {
  components: {
    MonthPicker,
    Confirm,
  },
  data() {
    return {
      invalidLaunchDate: null,
      showConfirmLaunch: false,
      currentLaunchDate: null,
    };
  },
  props: {
    chapterState: {
      default: "capitolo",
    },
    currentItem: {
      default: null,
    },
  },
  created() {
    this.currentLaunchDate =
      Utils.getMonthYear(
        this.currentItem[
          this.chapterState == "capitolo" ? "chapterLaunch" : "coreGroupLaunch"
        ]["prev"]
      ) ?? Utils.getMonthYear(new Date());
  },
  methods: {
    close() {
      this.$emit("close");
      this.invalidLaunchDate = false;
    },
    launch(response) {
      if (response) {
        this.$emit("launch", this.currentLaunchDate);
      } else {
        this.$emit("launch", false);
      }
    },
    setCurrentLaunchDate(date) {
      if (new Date(date) > new Date()) {
        this.invalidLaunchDate =
          "La data di lancio non può essere superiore alla data attuale";
      } else {
        this.invalidLaunchDate = null;
      }
      this.currentLaunchDate = Utils.getMonthYear(date);
    },
  },
};
</script>