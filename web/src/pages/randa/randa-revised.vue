<template>
  <div v-if="randaRevised && randaRevised.randa_state === 'APPR'">
    <Randa :title="'Randa revised'" :randa="randaRevised" />
    <div class="d-flex justify-end px-12 pt-6">
      <v-btn
        v-if="!isNational"
        :disabled="
          !allRanaApproved ||
            (randaRevised.randa_state == 'APPR' && !isNational)
        "
        @click="showApproveRanda = true"
      >
        Approva randa
      </v-btn>
      <v-btn v-if="isNational" @click="showDisapproveRanda = true" >
        Rifiuta
      </v-btn>
    </div>

    <v-dialog v-model="showApproveRanda" width="500">
      <v-card>
        <v-card-title class="headline primary white--text" primary-title>
          Approva randa
        </v-card-title>
        <v-card-text class="pa-5">
          <v-textarea
            v-model="note"
            label="Nota randa"
            prepend-icon="mdi-tag"
          ></v-textarea>
          <div class="d-flex flex-row">
            <v-text-field
              label="Director"
              class="ma-2"
              v-for="i in (0, 4)"
              :key="i"
              v-model="directors[i - 1]"
            />
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
            >
              {{ $t("cancel") }}
            </v-btn>
            <v-btn
              type="submit"
              normal
              text
              color="primary"
              @click="approveRanda()"
              :disabled="!isFormValid()"
            >
              {{ $t("conferma") }}
            </v-btn>
          </div>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="showDisapproveRanda" width="500">
      <v-card>
        <v-card-title class="headline primary white--text" primary-title>
          Rifiuta randa
        </v-card-title>
        <v-card-text class="pa-5">
          <v-textarea
            v-model="refuseNote"
            label="Nota randa"
            prepend-icon="mdi-tag"
          ></v-textarea>
        </v-card-text>
        <v-card-actions class="d-flex justify-end align-center">
          <div width="100%">
            <v-btn
              type="submit"
              normal
              text
              color="primary"
              @click="showDisapproveRanda = false"
            >
              {{ $t("cancel") }}
            </v-btn>
            <v-btn
              type="submit"
              normal
              text
              color="primary"
              @click="refuseRanda()"
              :disabled="!refuseNote"
            >
              {{ $t("conferma") }}
            </v-btn>
          </div>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <div v-if="false" class="elevation-9 red_card">
      <v-icon v-on="on" small class="primary--text">mdi-alert</v-icon>
      Nota BNI:
      <!-- {{ rana.refuse_note }} -->
    </div>
  </div>
  <div v-else>
    <NoData :message="'Nessun randa da visualizzare'" />
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
      randaRevised: null,
      allRanaApproved: false,
      note: "",
      refuseNote: "",
      directors: [0, 0, 0, 0],
      showApproveRanda: false,
      showDisapproveRanda: false,
      isNational: false
    };
  },
  created() {
    setTimeout(() => {
      this.fetchRandaRevised();
      this.isNational = this.$store.getters["getRegion"].role === "NATIONAL";
    });
  },
  methods: {
    async refuseRanda() {
      let region = this.$store.getters["getRegion"].id;
      this.randaRevised = await ApiServer.put(region + "/refuse-randa", {
        refuseNote: this.refuseNote
      });
      this.showDisapproveRanda = false;
    },
    async fetchRandaRevised() {
      let region = this.$store.getters["getRegion"].id;
      this.randaRevised = await ApiServer.get(region + "/randa-revised");
      debugger;
      if (!this.randaRevised) {
        this.$store.commit("snackbar/setData", {
          messageLabel: "no_randa_to_show",
          status: "error"
        });
      } else {
        this.allRanaApproved = this.randaRevised.all_approved;
      }
    },
    async approveRanda() {
      let region = this.$store.getters["getRegion"].id;
      this.randaRevised = await ApiServer.put(region + "/approve-randa", {
        note: this.note,
        directors: this.directors.join(","),
        timeslot: this.randaRevised.timeslot
      });

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
    }
  }
};
</script>
