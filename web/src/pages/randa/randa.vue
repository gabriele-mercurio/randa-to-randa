<template>
  <div>
    <div class="d-flex px-6 mt-3 d-print-none justify-end">
      <v-btn icon @click="getXLSX()"> <v-icon>mdi-file-excel</v-icon></v-btn>
      <v-btn icon @click="print()"> <v-icon>mdi-cloud-print</v-icon></v-btn>
    </div>
    <Randa :title="'Randa'" :randa.sync="randa" :layout="'split'" />
    <div v-if="!isNational" class="pr-6 pb-6 d-flex justify-end">
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
import XLSX from "xlsx";

export default {
  components: {
    Randa,
    NoData,
    XLSX
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
      this.isNational = this.$store.getters["getIsNational"];
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
    },
    async getXLSX() {
      var workbook = XLSX.utils.book_new();
      var table = document.querySelector("#table_wrapper");
      var sheet = XLSX.utils.table_to_sheet(table);
      XLSX.utils.book_append_sheet(workbook, sheet, "Sheet");
      var wbout = XLSX.writeFile(
        workbook,
        "RANDA_" + this.randa.year + "_" + this.randa.timeslot + ".xlsx",
        {
          type: "file",
          bookType: "xlsx"
        }
      );
    },

    print() {
      print();
    }
  }
};
</script>

