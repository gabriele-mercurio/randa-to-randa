<template
  ><v-card>
    <v-card-title class="headline primary white--text" primary-title>
      Sospensione capitolo
    </v-card-title>
    <v-card-text>
      <p class="pt-9">
        Se si desidera, immettere una data previsionale di ripresa capitolo.
      </p>
      <MonthPicker
        :defLabel="'Data prevista riavvio capitolo'"
        v-on:setdate="setPrevResumeDate(chapterData)"
        :defDate.sync="prevResumeDate"
      />
    </v-card-text>
    <v-card-actions class="d-flex justify-end align-center">
      <div width="100%">
        <v-btn
          type="submit"
          normal
          text
          color="primary"
          @click="closeSuspendDialog(false)"
        >
          {{ $t("cancel") }}
        </v-btn>
        <v-btn type="submit" normal text color="primary" @click="doSuspend()">
          {{ $t("confirm") }}
        </v-btn>
      </div>
    </v-card-actions>
  </v-card>
</template>

<script>
import MonthPicker from "./MonthPicker";
import ApiServer from "../services/ApiServer";

export default {
  data() {
    return {
      chapterData: null,
      prevResumeDate: null
    };
  },
  components: {
    MonthPicker
  },
  props: {
    chapter: null
  },
  methods: {
    async doSuspend() {
      let res1 = await ApiServer.put("api/" + "chapter/" + this.chapter.id + "/suspend");

      if (this.chapterData.prevResumeDate) {
        let result = await ApiServer.put("api/" + 
          "chapter/" + this.chapter.id,
          this.chapterData
        );
      }

      this.$store.commit("snackbar/setData", {
        messageLabel: "chapter_suspended",
        status: "success"
      });

      this.closeSuspendDialog(true);
    },
    closeSuspendDialog(isSuspended) {
      this.$emit("close", isSuspended);
    },
    setPrevResumeDate(date) {
      this.chapterData["prevResumeDate"] = date;
    }
  },
  watch: {
    chapter: {
      handler: function(newVal, oldVal) {
        this.chapterData = newVal;
      },
      deep: true,
      immediate: true
    }
  }
};
</script>
