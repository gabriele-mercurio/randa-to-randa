<template>
  <div>
    <Randa :title="'Randa dream'" :randa="randaDream" />
    <NoData v-if="noData" :message="'Nessun randa dream'" />
  </div>
</template>
<script>
import Utils from "../../services/Utils";
import ApiServer from "../../services/ApiServer";
import Randa from "../../components/Randa";
import NoData from "../../components/NoData";

export default {
  components: {
    Randa,
    NoData
  },
  data() {
    return {
      randaDream: null,
      noData: false
    };
  },
  created() {
    setTimeout(() => {
      this.fetchRandaDream();
    });
  },
  methods: {
    async fetchRandaDream() {
      let region = this.$store.getters["getRegion"].id;
      this.randaDream = await ApiServer.get("api/" + region + "/randa-dream");
      if (!this.randaDream) {
        this.noData = true;
      }
    }
  }
};
</script>
