<template>
  <div>
    <Randa :title="'Randa'" :randa="randa" :layout="'split'" />
    <div v-if="isNational">
      <v-btn @click="showApproveRanda = true"> Approva</v-btn>
    </div>
    <NoData v-if="noData" :message="'Nessun randa'" />
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
      randa: null,
      showApproveRanda: false,
      allRanaApproved: false,
      isNational: false,
      noData: false
    };
  },
  created() {
    setTimeout(() => {
      this.fetchRanda();
      this.isNational = this.$store.getters["getRegion"].role === "NATIONAL";
    });
  },
  methods: {
    async fetchRanda() {
      let region = this.$store.getters["getRegion"].id;
      this.randa = await ApiServer.get(region + "/randa");
      if (!this.randa) {
        this.noData = true;
      } else {
        this.noData = false;
      }
    }
  }
};
</script>
