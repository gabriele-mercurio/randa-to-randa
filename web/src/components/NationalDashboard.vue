<template>
  <div v-if="nationalDashboard">
    <div class="d-flex px-6 pt-6">
      <div>
        <span class="number font-weight-bold font-italic">{{ nationalDashboard.regions.current_t_approved.length }}</span
        ><span> Approvati</span>
      </div>
      <div>
        <span class="number ml-4 font-weight-bold font-italic">{{ nationalDashboard.regions.current_t_refused.length }}</span
        ><span> Rifiutati</span>
      </div>
      <div>
        <span class="number ml-4 font-weight-bold font-italic">{{ nationalDashboard.regions.current_t_doing.length }}</span
        ><span> In corso</span>
      </div>
    </div>
    <DashboardTable
      v-if="
        nationalDashboard.regions.current_t_approved &&
          nationalDashboard.regions.current_t_approved.length
      "
      :data="nationalDashboard.regions.current_t_approved"
      :link="true"
    />
    <DashboardTable
      v-if="
        nationalDashboard.regions.current_t_refused &&
          nationalDashboard.regions.current_t_refused.length
      "
      :data="nationalDashboard.regions.current_t_refused"
    />
    <DashboardTable
      v-if="
        nationalDashboard.regions.current_t_doing &&
          nationalDashboard.regions.current_t_doing.length
      "
      :data="nationalDashboard.regions.current_t_doing"
    />
    <DashboardTable
      v-if="
        nationalDashboard.regions.others &&
          nationalDashboard.regions.others.length
      "
      :data="nationalDashboard.regions.others"
      :others="true"
    />
  </div>
</template>
<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
import DashboardTable from "../components/DashboardTable";

export default {
  data() {
    return {
      nationalDashboard: null,
      Utils: null
    };
  },
  components: {
    DashboardTable
  },
  methods: {
    async fetchNationalDashboard() {
      this.nationalDashboard = await ApiServer.get("nationalDashboard");
    }
  },
  created() {
    this.Utils = Utils;
    this.fetchNationalDashboard();
  }
};
</script>
<style lang="scss">
#nationalDashboard {
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
