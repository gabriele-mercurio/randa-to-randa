<template
  >
  <div>
    <div v-if="randaRevised && randa">
      <v-row class="d-print-none">
        <v-col cols="1" class="pa-0 ma-0 d-flex justify-center align-center" v-if="isNational">
          <v-btn small fab color="primary" @click="goToPrevRegion()">
            <v-icon>mdi-arrow-left</v-icon>
          </v-btn>
        </v-col>
        <v-col cols="10" :class="!isNational ? 'offset-1': ''">
          <v-tabs centered class="my-6" v-model="tab">
            <v-tab>Riepilogo</v-tab>
            <v-tab>Randa</v-tab>
          </v-tabs>
        </v-col>
        <v-col cols="1" class="pa-0 ma-0 d-flex justify-center align-center" v-if="isNational">
          <v-btn fab small color="primary" @click="goToNextRegion()">
            <v-icon>mdi-arrow-right</v-icon>
          </v-btn>
        </v-col>
      </v-row>

      <v-tabs-items v-model="tab">
        <v-tab-item>
          <Randa :title="'Randa revised'" :randa="randaRevised" :showTotal="false" />
          <div v-if="isNational" class="d-flex justify-end pa-4 pt-0 px-10">
            <v-btn
              @click="showDisapproveRanda = true"
              :disabled="randa.randa_state == 'REFUSED'"
              class="mr-3"
            >Rifiuta</v-btn>
            <v-btn
              color="primary"
              @click="showConfirmVerifyRanda = true"
              :disabled="randa.randa_verified"
            >Approva</v-btn>
        </v-tab-item>
        <v-tab-item>
          <Randa :title="'Randa'" :randa="randa" :layout="'split'" :showTotal="false" />
          
          </div>
          <!-- <v-btn v-if="!isNational" @click="showApproveRanda = true">Approva</v-btn> -->
          <v-dialog v-model="showDisapproveRanda" width="500">
            <v-card>
              <v-card-title class="line primary white--text" primary-title>Rifiuta randa</v-card-title>
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
        </v-tab-item>
      </v-tabs-items>

      <div class="d-flex justify-end px-6 mb-6 d-print-none">
        <v-btn
          color="primary"
          v-if="!isNational && role !== 'ASSISTANT' && tab == 0"
          :disabled="
            !randaRevised.all_approved ||
            !(randaRevised.randa_state == 'TODO' || randaRevised.randa_state == 'REFUSED')
          "
          @click="showApproveRanda = true"
        >Approva randa</v-btn>
        <!-- <v-btn @click="getXLSX()"> Scarica XLSX </v-btn> -->
      </div>
      <v-dialog v-model="showApproveRanda" width="500">
        <v-card>
          <v-card-title class="headline primary white--text" primary-title>Conferma approvazione</v-card-title>
          <v-card-text class="pa-5">
            <v-textarea solo v-model="note" label="Nota randa" prepend-inner-icon="mdi-pen"></v-textarea>
            <div class="d-flex flex-column">
              <div>
                <v-icon>mdi-account</v-icon>Directors
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
    <template v-else>
      <Loader v-if="loading" />
      <NoData v-else :message="'Nessun randa da visualizzare'" />
    </template>

    <Confirm
      :message="'Approvare il randa?'"
      :show.sync="showConfirmVerifyRanda"
      v-on:dialogResponse="verifyRanda"
    />

    <v-snackbar v-model="randaVerifiedSuccessSnackbar" :timeout="timeout" top right>
      <v-icon color="green">mdi-check</v-icon>Randa approvato!
      <v-btn color="white" icon @click="randaVerifiedSuccessSnackbar = false">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-snackbar>
  </div>
</template>
<script>
import Utils from "../../services/Utils";
import ApiServer from "../../services/ApiServer";
import Randa from "../../components/Randa";
import NoData from "../../components/NoData";
import Loader from "../../components/Loader";
import Confirm from "../../components/Confirm";
import XLSX from "xlsx";

export default {
  components: {
    Randa,
    NoData,
    Loader,
    Confirm,
  },
  data() {
    return {
      randaRevised: null,
      randa: null,
      note: "",
      refuseNote: "",
      directors: [0, 0, 0, 0],
      showApproveRanda: false,
      showDisapproveRanda: false,
      isNational: false,
      oldRandas: [],
      tab: null,
      regions: [],
      role: null,
      loading: true,
      showConfirmVerifyRanda: false,
      randaVerifiedSuccessSnackbar: false,
      timeout: 3000,
    };
  },
  created() {
    setTimeout(async () => {
      await this.fetchAllData();
      if (
        window.location.search &&
        window.location.search.indexOf("recap=true")
      ) {
        this.tab = 1;
      }
      this.isNational = this.$store.getters["getIsNational"];
      this.role = this.$store.getters["getRegion"].role;
    }, 200);
  },
  methods: {
    async fetchAllData() {
      await this.fetchRegions();
      await this.fetchRanda();
      await this.fetchRandaRevised();
      await this.fetchOldRandas();
      this.loading = false;
    },
    async verifyRanda() {
      let region = this.$store.getters["getRegion"].id;
      let verifiedRanda = await ApiServer.put(
        "api/" + region + "/verify-randa"
      );
      if (!verifiedRanda.error) {
        this.randa.randa_verified = true;
        this.randaVerifiedSuccessSnackbar = true;
      }
    },
    goToPrevRegion() {
      for (let k = 0; k < this.regions.length; k++) {
        if (this.regions[k].name === this.randa.region) {
          if (k > 0) {
            this.$store.commit("setRegion", this.regions[k - 1]);
            this.fetchAllData();
          }
        }
      }
    },
    goToNextRegion() {
      for (let k = 0; k < this.regions.length; k++) {
        if (this.regions[k].name === this.randa.region) {
          if (k < this.regions.length - 1) {
            this.$store.commit("setRegion", this.regions[k + 1]);
            this.fetchAllData();
          }
        }
      }
    },
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
    async fetchRegions() {
      let result = await ApiServer.get("api/" + "nationalDashboard");
      if (!result.error) {
        this.regions = result.regions.current_t_approved;
      }
      return true;
    },
    async fetchRandaRevised() {
      let region = this.$store.getters["getRegion"].id;
      this.randaRevised = await ApiServer.get(
        "api/" + region + "/randa-revised"
      );
      this.note = this.randaRevised.note;
      this.directors = this.randaRevised.directors_previsions
        ? this.randaRevised.directors_previsions.split(",")
        : [0, 0, 0, 0];
      if (!this.randaRevised) {
        this.$store.commit("snackbar/setData", {
          messageLabel: "no_randa_to_show",
          status: "error",
        });
      }
      return true;
    },

    async fetchOldRandas() {
      let region = this.$store.getters["getRegion"].id;
      this.oldRandas[0] = await ApiServer.get(
        "api/" + region + "/randa-revised?timeslot=T2"
      );
      return true;
    },
    async fetchRanda() {
      let region = this.$store.getters["getRegion"].id;
      this.randa = await ApiServer.get("api/" + region + "/randa");
      if (!this.randa) {
        this.$store.commit("snackbar/setData", {
          messageLabel: "no_randa_to_show",
          status: "error",
        });
      }
      return true;
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
