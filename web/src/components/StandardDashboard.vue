<template>
  <div v-if="standardDashboard">
    <div class="d-flex px-6 pt-6">
      <div>
        <span class="number font-weight-bold font-italic">{{ standardDashboard.chapters_compositions.CHAPTER }}</span
        ><span> Capitoli</span>
      </div>
      <div>
        <span class="number ml-4 font-weight-bold font-italic">{{ standardDashboard.chapters_compositions.CORE_GROUP }}</span
        ><span> Core groups</span>
      </div>
      <div>
        <span class="number ml-4 font-weight-bold font-italic">{{ standardDashboard.chapters_compositions.PROJECT }}</span
        ><span> Progetti</span>
      </div>
    </div>
  </div>
</template>
<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import DashboardTable from "../components/DashboardTable";

export default {
  data() {
    return {
      standardDashboard: null,
      Utils: null
    };
  },
  components: {
    DashboardTable
  },
  methods: {
    async fetchStandardDashboard() {
      this.standardDashboard = await ApiServer.get("api/" + this.$store.getters["getRegion"].id + "/standardDashboard");
    }
  },
  created() {
    this.Utils = Utils;
    setTimeout(() => {
        this.fetchStandardDashboard();
    });
  }
};
</script>
<style lang="scss">
#standardDashboard {
  tr {
    td {
      border-bottom: 1px solid lightgray;
      height: 25px;
    }
    th {
      background-color: lighten($lightred, 30%);
      height: 25px;
      line-height: 25px;
    }
  }
  .number {
      font-size: 40px!important;
  }
}
</style>
