<template
  >
  <div>
    <div
      v-if="randaRevised || (randa && (randa.randa_state === 'APPR' || randa.randa_state === 'REFUSED'))"
    >
      <div v-if="isNational">
        <Randa :title="'Randa'" :randa="randa" :layout="'split'" :showTotal="false" />
        <div v-if="isNational" class="d-flex justify-end pa-4 pt-0 px-10">
          <v-btn
            @click="showDisapproveRanda = true"
            :disabled="randa.randa_state == 'REFUSED'"
          >Rifiuta</v-btn>
        </div>
        <v-btn v-if="!isNational" @click="showApproveRanda = true">Approva</v-btn>
        <v-dialog v-model="showDisapproveRanda" width="500">
          <v-card>
            <v-card-title class="headline primary white--text" primary-title>Rifiuta randa</v-card-title>
            <v-card-text class="pa-5">
              <v-textarea v-model="refuseNote" label="Nota randa" prepend-icon="mdi-tag"></v-textarea>
            </v-card-text>
            <v-card-actions class="d-flex justify-end align-center">
              <div width="100%">
                <v-btn
                  type="submit"
                  normal
                  text
                  color="primary"
                  @click="showDisapproveRanda = false"
                >{{ $t("cancel") }}</v-btn>
                <v-btn
                  type="submit"
                  normal
                  text
                  color="primary"
                  @click="refuseRanda()"
                  :disabled="!refuseNote"
                >{{ $t("conferma") }}</v-btn>
              </div>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>
      <div v-else>
        <Randa :title="'Randa revised'" :randa="randaRevised" :showTotal="false" />
        <div class="d-flex justify-end px-12 pt-6 mb-6">
          <v-btn
            color="primary"
            v-if="!isNational"
            :disabled="
              !allRanaApproved ||
                (randaRevised.randa_state == 'APPR' && !isNational)
            "
            @click="showApproveRanda = true"
          >Approva randa</v-btn>
          <!-- <v-btn @click="getXLSX()"> Scarica XLSX </v-btn> -->
        </div>
      </div>

      <v-dialog v-model="showApproveRanda" width="500">
        <v-card>
          <v-card-title class="headline primary white--text" primary-title>Approva randa</v-card-title>
          <v-card-text class="pa-5">
            <v-textarea solo v-model="note" label="Nota randa" prepend-inner-icon="mdi-pen"></v-textarea>
            <div class="d-flex flex-column">
              <div>
                <v-icon >mdi-account</v-icon>Directors
              </div>
              <div class="d-flex flex-row">
                <v-text-field
                  :label="'T' + (i)"
                  class="ma-2"
                  v-for="i in (0, 4)"
                  :key="i"
                  type="number"
                  v-model="directors[i - 1]"
                />
              </div>
            </div>
          </v-card-text>
          <v-card-actions class="d-flex justify-end align-center">
            <div width="100%">
              <v-btn
                type="submit"
                normal
                text
                color="primary"
                @click="showApproveRanda = false"
              >{{ $t("cancel") }}</v-btn>
              <v-btn
                type="submit"
                normal
                text
                color="primary"
                @click="approveRanda()"
                :disabled="!isFormValid()"
              >{{ $t("conferma") }}</v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <div v-if="false" class="elevation-9 red_card">
        <v-icon v-on="on" small class="primary--text">mdi-alert</v-icon>Nota BNI:
        <!-- {{ rana.refuse_note }} -->
      </div>
    </div>
    <!-- <div v-if="oldRandas.length">
      <div v-for="randa in oldRandas">
        <Randa :title="'Randa'" :randa="randa" :showTotal="false"/>
      </div>
    </div>-->
    <NoData v-else :message="'Nessun randa da visualizzare'" />
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
  },
  data() {
    return {
      randaRevised: null,
      randa: null,
      allRanaApproved: false,
      note: "",
      refuseNote: "",
      directors: [0, 0, 0, 0],
      showApproveRanda: false,
      showDisapproveRanda: false,
      isNational: false,
      oldRandas: [],
    };
  },
  created() {
    setTimeout(() => {
      this.isNational = this.$store.getters["getIsNational"];
      if (this.isNational) {
        this.fetchRanda();
      } else {
        this.fetchRandaRevised();
        this.fetchOldRandas();
      }
    }, 200);
  },
  methods: {
    async refuseRanda() {
      let region = this.$store.getters["getRegion"].id;
      this.randaRevised = await ApiServer.put(
        "api/" + region + "/refuse-randa",
        {
          refuseNote: this.refuseNote,
        }
      );
      this.randa.randa_state = "TODO";
      this.showDisapproveRanda = false;
    },
    async fetchRandaRevised() {
      let region = this.$store.getters["getRegion"].id;
      this.randaRevised = await ApiServer.get(
        "api/" + region + "/randa-revised"
      );
      this.note = this.randaRevised.note;
      this.directors = this.randaRevised.directors_previsions.split(",");
      if (!this.randaRevised) {
        this.$store.commit("snackbar/setData", {
          messageLabel: "no_randa_to_show",
          status: "error",
        });
      } else {
        this.allRanaApproved = this.randaRevised.all_approved;
      }
    },
    async getXLSX() {
      let sheet = XLSX.utils.json_to_sheet(this.randaRevised.chapters);
      const wb = { SheetNames: ["Export"], Sheets: {}, Props: {} };
      wb.Sheets["Export"] = sheet;
      var wbout = XLSX.write(wb, {
        type: "file",
      });
    },
    async fetchOldRandas() {
      let region = this.$store.getters["getRegion"].id;
      this.oldRandas[0] = await ApiServer.get(
        "api/" + region + "/randa-revised?timeslot=T2"
      );
    },
    async fetchRanda() {
      let region = this.$store.getters["getRegion"].id;
      this.randa = await ApiServer.get("api/" + region + "/randa");
      if (!this.randa) {
        this.$store.commit("snackbar/setData", {
          messageLabel: "no_randa_to_show",
          status: "error",
        });
      } else {
        this.allRanaApproved = this.randa.all_approved;
      }
    },

    async approveRanda() {
      let region = this.$store.getters["getRegion"].id;
      this.randaRevised = await ApiServer.put(
        "api/" + region + "/approve-randa",
        {
          note: this.note,
          directors: this.directors.join(","),
          timeslot: this.randaRevised.timeslot,
        }
      );

      if (!this.randaRevised.error) {
        this.$router.push("/randa/randa");
      }
    },
    isFormValid() {
      for (let i = 0; i < 4; i++) {
        if (this.directors[i] == null || this.directors[i] == "") {
          return false;
        }
      }
      if (!this.note) return false;
      return true;
    },
  },
};
</script>
