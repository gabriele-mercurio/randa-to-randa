<template>
  <v-card class="ma-6 elevation-2">
    <v-card-title>
      <span class="font-weight-bold">{{ getTitle() }}</span>
    </v-card-title>
    <v-card-text class="pa-0">
      <v-data-table
        v-if="data"
        :headers="headers"
        disable-pagination
        hide-default-footer
        id="nationalDashboard"
      >
        <template v-slot:body>
          <tr v-for="region in data" :key="region.id">
            <td class="pa-2">{{ region.name }}</td>
            <td class="pa-2 text-center">{{ region.n_chapters }}</td>
            <td class="pa-2 text-center">{{ region.n_members_per_chapters }}</td>
            <td class="pa-2 text-center">{{ region.n_core_groups }}</td>
            <td class="pa-2 text-center">{{ region.n_members_per_core_groups }}</td>
            <td class="pa-2 text-center">{{ region.n_projects }}</td>
            <td class="pa-2 text-center">{{ region.n_all_chapters }}</td>
            <td class="pa-2 text-center">{{ region.n_members_total }}</td>
            <td class="pa-2">
              {{ region.randa_timeslot }}
              <span v-if="region.randa_verified">Approvato</span>
              <span v-else>{{ Utils.getRandaState(region.randa_state, isNational) }}</span>
            </td>
            <td v-if="link" class="text-right">
              <v-btn @click="goToRanda(region)" color="primary" normal text>Vai a randa</v-btn>
            </td>
          </tr>
        </template>
      </v-data-table>
    </v-card-text>
  </v-card>
</template>
<script>
import Utils from "../services/Utils";
export default {
  data() {
    return {
      Utils: Utils,
      isNational: false,
      headers: [
        {
          text: "Region",
          value: "name",
          sortable: false,
        },
        {
          text: "Capitoli",
          align: "center",
          sortable: false,
        },
        {
          text: "Membri capitoli",
          align: "center",
          sortable: false,
        },
        {
          text: "Core groups",
          align: "center",
          sortable: false,
        },
        {
          text: "Membri core group",
          align: "center",
          sortable: false,
        },
        {
          text: "Progetti",
          align: "center",
          sortable: false,
        },
        {
          text: "Tutti",
          align: "center",
          sortable: false,
        },
        {
          text: "Membri totali",
          align: "center",
          sortable: false,
        },
        {
          text: "Stato randa",
          sortable: false,
        },
      ],
    };
  },
  props: {
    data: null,
    others: false,
    link: {
      default: false,
    },
  },
  created() {
    if (this.link) {
      this.headers.push({
        text: "",
        value: "",
        sortable: false,
      });
    }
    setTimeout(() => {
      this.isNational = this.$store.getters["getIsNational"];
      debugger;
    });
  },
  methods: {
    getTitle() {
      if (this.data && this.data.length) {
        if (this.others) return "Altri";
        return (
          this.data[0].randa_timeslot +
          " " +
          this.getRandaState()
        );
      }
    },
    getRandaState() {
      return this.data[0].randa_verified ? 'Approvati' : Utils.getRandaState(this.data[0].randa_state, this.isNational);
    },
    goToRanda(region_data) {
      let region = {};
      region["id"] = region_data.id;
      this.$store.commit("setRegion", region);
      setTimeout(() => {
        this.$router.push("/randa/randa-revised");
      });
    },
  },
};
</script>
