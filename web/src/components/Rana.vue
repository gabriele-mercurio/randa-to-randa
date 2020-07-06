<template>
  <div id="rana" v-if="rana">
    <div class="d-flex justify-space-between align-end">
      <div>
        <div>
          Membri inizali:
          <span class="font-italic font-weight-light mr-2">{{
            rana.initialMembers
          }}</span>
        </div>
        <span class="font-weight-bold">{{ rana.timeslot }}: </span>
        <span v-if="rana">{{ getRanaState(rana.state) }}</span>
      </div>
      <div v-if="editable">
        <div class="d-flex flex-row align-center">
          <div class="circle background-yellow"></div>
          <div class="ml-1">Approvato</div>
        </div>
        <div class="d-flex flex-row align-center">
          <div class="circle background-green"></div>
          <div class="ml-1">Consuntivo</div>
        </div>

        <div class="d-flex flex-row align-center">
          <div class="circle stroke-green"></div>
          <div class="ml-1">Da compilare</div>
        </div>
      </div>
    </div>

    <v-data-table disable-pagination hide-default-footer>
      <template v-slot:body>
        <tbody>
          <tr>
            <td rowspan="3">Nuovi</td>
            <td
              colspan="3"
              v-for="i in (0, 4)"
              :key="i"
              class="text-center pa-0"
              :class="{
                bordered: !isPastTimeslot(i, rana.timeslot) && editable
              }"
            >
              <div class="border-bottom background-grey pa-1">T{{ i }}</div>
              <v-row class="pa-0 ma-0">
                <v-col
                  class="pa-0 d-flex justify-center align-center"
                  :class="{
                    'border-right': isPastTimeslot(i, rana.timeslot),
                    'background-yellow': isPastTimeslot(i, rana.timeslot),
                    'stroke-green': canApprove(i, 4) || canPropose(i, 4)
                  }"
                >
                  <v-text-field
                    :placeholder="'T' + i"
                    :disabled="!canApprove(i, 4) && !canPropose(i, 4)"
                    type="number"
                    @keyup="proposeMonths($event, i, 'newMembers')"
                    v-model="timeslotAggregations.newMembers.PREV[i]"
                  />
                </v-col>
                <v-col
                  v-if="isPastTimeslot(i, rana.timeslot)"
                  class="pa-0 background-green font-italic font-weight-light disabled d-flex justify-center align-center"
                  >{{
                    timeslotAggregations.newMembers.CONS[i] !== null
                      ? timeslotAggregations.newMembers.CONS[i]
                      : "X"
                  }}</v-col
                >
              </v-row>
            </td>
          </tr>

          <!-- MONTHS row -->
          <tr>
            <td
              v-for="i in (0, 12)"
              :key="i"
              class="text-center pa-0"
              :class="{
                bordered: !isPastMonth(i, rana.timeslot) && editable
              }"
            >
              <!-- month labels -->
              <div class="border-bottom background-grey pa-1">M{{ i }}</div>

              <!-- new members data -->
              <v-row class="pa-0 ma-0">
                <v-col
                  class="pa-0 d-flex justify-center align-center"
                  :class="{
                    'border-righ': isPastMonth(i, rana.timeslot),
                    'background-yellow': isPastMonth(i, rana.timeslot),
                    'stroke-green': canApprove(i, 12) || canPropose(i, 12)
                  }"
                >
                  <v-text-field
                    :placeholder="'M' + i"
                    :disabled="!canApprove(i, 12) && !canPropose(i, 12)"
                    type="number"
                    v-model="rana.newMembers.PREV['m' + i]"
                    @keyup="calculateTimeslot($event, i, 'newMembers')"
                  />
                </v-col>
                <v-col
                  v-if="isPastMonth(i, rana.timeslot)"
                  class="pa-0 background-green font-italic font-weight-light disabled d-flex justify-center align-center"
                  >{{ getConsumptive(i, "newMembers") }}
                </v-col>
              </v-row>
            </td>
          </tr>
        </tbody>

        <tbody>
          <tr>
            <td rowspan="3">Uscite</td>
            <td
              colspan="3"
              v-for="i in (0, 4)"
              :key="i"
              class="text-center pa-0"
              :class="{
                bordered: !isPastTimeslot(i, rana.timeslot) && editable
              }"
            >
              <div class="border-bottom background-grey pa-1">T{{ i }}</div>
              <v-row class="pa-0 ma-0">
                <v-col
                  class="pa-0 d-flex justify-center align-center"
                  :class="{
                    'border-right': isPastTimeslot(i, rana.timeslot),
                    'background-yellow': isPastTimeslot(i, rana.timeslot),
                    'stroke-green': canApprove(i, 4) || canPropose(i, 4)
                  }"
                >
                  <v-text-field
                    :placeholder="'T' + i"
                    :disabled="!canApprove(i, 4) && !canPropose(i, 4)"
                    type="number"
                    @keyup="proposeMonths($event, i, 'retentions')"
                    v-model="timeslotAggregations.retentions.PREV[i]"
                  />
                </v-col>
                <v-col
                  v-if="isPastTimeslot(i, rana.timeslot)"
                  class="pa-0 background-green font-italic font-weight-light disabled d-flex justify-center align-center"
                  >{{
                    timeslotAggregations.retentions.CONS[i] !== null
                      ? timeslotAggregations.retentions.CONS[i]
                      : "X"
                  }}</v-col
                >
              </v-row>
            </td>
          </tr>

          <!-- MONTHS row -->
          <tr>
            <td
              v-for="i in (0, 12)"
              :key="i"
              class="text-center pa-0"
              :class="{
                'border-right-bold': i % 3 == 0,
                bordered: !isPastMonth(i, rana.timeslot) && editable
              }"
            >
              <!-- month labels -->
              <div class="border-bottom background-grey pa-1">M{{ i }}</div>

              <!-- retentions data -->
              <v-row class="pa-0 ma-0">
                <v-col
                  class="pa-0 d-flex justify-center align-center"
                  :class="{
                    'border-righ': isPastMonth(i, rana.timeslot),
                    'background-yellow': isPastMonth(i, rana.timeslot),
                    'stroke-green': canApprove(i, 12) || canPropose(i, 12)
                  }"
                >
                  <v-text-field
                    :placeholder="'M' + i"
                    :disabled="!canApprove(i, 12) && !canPropose(i, 12)"
                    type="number"
                    v-model="rana.retentions.PREV['m' + i]"
                    @keyup="calculateTimeslot($event, i, 'retentions')"
                  />
                </v-col>
                <v-col
                  v-if="isPastMonth(i, rana.timeslot)"
                  class="pa-0 background-green font-italic font-weight-light disabled d-flex justify-center align-center"
                  >{{ getConsumptive(i, "retentions") }}
                </v-col>
              </v-row>
            </td>
          </tr>
        </tbody>
        <tbody>
          <tr>
            <td rowspan="1">Totale membri</td>
            <td
              v-for="i in (0, 12)"
              :key="i"
              class="text-center pa-0 background-red"
              :class="{ 'border-right-bold': i % 3 == 0 }"
            >
              {{ evaluateMembers(i) }}
            </td>
          </tr>
        </tbody>
      </template>
      <template slot="no-data">
        <tr style="display: none;"></tr>
      </template>
      <template slot="footer" v-if="canShowFooter()">
        <div class="d-flex justify-end my-4">
          <v-btn
            type="submit"
            normal
            color="primary"
            @click="resetProposal()"
            class="mr-2"
          >
            {{ $t("reset") }}
          </v-btn>
          <v-btn
            type="submit"
            normal
            color="primary"
            :disabled="nullFields()"
            @click="sendProposalOrApprovation()"
            v-if="rana.state !== 'APPR'"
          >
            <span v-if="role === 'ASSISTANT'">{{ $t("send_proposal") }}</span>
            <span v-if="(role == 'ADMIN' || role == 'EXECUTIVE') && rana.state == 'PROP'">{{ $t("approve_proposal") }}</span>
            <span
              v-if="
                (role == 'ADMIN' || role == 'EXECUTIVE') && rana.state == 'TODO'
              "
              >{{ $t("approve_without_proposal") }}</span
            >
          </v-btn>
          <v-btn
            color="primary"
            @click="disapproveRana()"
            v-if="
              (role == 'ADMIN' || role == 'EXECUTIVE') && rana.state === 'APPR'
            "
            >{{ $t("disapprove_rana") }}
          </v-btn>
        </div>
      </template>
    </v-data-table>

    <div v-if="rana.refuse_note" class="elevation-9 red_card">
      <v-icon v-on="on" small class="primary--text">mdi-alert</v-icon>
      Nota BNI:
      {{ rana.refuse_note }}
    </div>
  </div>
</template>
<script>
import Utils from "../services/Utils";
import ApiServer from "../services/ApiServer";
import Snackbar from "../components/Snackbar";

export default {
  components: {
    Snackbar
  },
  data() {
    return {
      timeslotAggregations: {
        newMembers: {
          PREV: [],
          CONS: []
        },
        retentions: {
          PREV: [],
          CONS: []
        }
      },
      data: {
        PREV: [],
        CONS: []
      },
      ranaBackup: null,
      snackbarData: {
        showSnackbar: true,
        snackbarState: null,
        snackbarMessageLabel: ""
      },

      isProposed: false,
      currentTimeslot: null
    };
  },
  props: {
    defData: [],
    rana: null,
    editable: false,
    prevRana: null,
    ranaType: null
  },
  computed: {
    nextTimeslot() {
      return "T" + (this.currentTimeslot.substr(-1) * 1 + 1);
    },
    role: {
      get() {
        if (this.$store.getters["getRegion"]) {
          return this.$store.getters["getRegion"].role;
        }
        return "";
      },
      set(role) {}
    }
  },
  methods: {
    getRanaState(state) {
      switch (state) {
        case "TODO":
          if (this.role === "ASSISTANT") {
            return "Proposta";
          } else {
            return "Nessuna proposta. Compila approvazione.";
          }
        case "PROP":
          if (this.role === "ASSISTANT") {
            return "Proposto";
          } else {
            return "Proposto. Compila approvazione.";
          }
        case "APPR":
          return "Approvato";
      }
    },
    async disapproveRana() {
      let res = await ApiServer.put(this.rana.id + "/disapprove");
      if (!res.error) {
        this.$emit("fetchRana");
      }
    },
    evaluateMembers(m) {
      let sum = this.rana.initialMembers;
      for (let i = 0; i < m; i++) {
        sum +=
          (this.rana.newMembers.PREV["m" + i]
            ? this.rana.newMembers.PREV["m" + i]
            : 0) -
          (this.rana.retentions.PREV["m" + i]
            ? this.rana.retentions.PREV["m" + i]
            : 0);
      }
      return sum;
    },
    getConsumptive(i, values) {
      if (
        this.rana[values].CONS["m" + i] === null ||
        this.rana[values].CONS["m" + i] === ""
      )
        return "X";
      return this.rana[values].CONS["m" + i];
    },
    canShowFooter() {
      switch (this.role) {
        case "ASSISTANT":
          return this.rana.state === "TODO";
        case "EXECUTIVE":
        default:
          return true;
      }
    },
    proposedForExecutives() {
      let r = this.rana.state === "PROP" && this.role != "ASSISTANT";
      return r;
    },
    nullFields() {
      return false;
      // for (let m of Object.keys(this.rana.newMembers.PREV)) {
      //   if (
      //     m >= "m" + Utils.getFirstTimeslotMonth(this.nextTimeslot) &&
      //     this.rana.newMembers.PREV[m].toString() == ""
      //   ) {
      //     return true;
      //   }
      // }
      // for (let m of Object.keys(this.rana.retentions.PREV)) {
      //   if (
      //     m >= "m" + Utils.getFirstTimeslotMonth(this.nextTimeslot) &&
      //     this.rana.retentions.PREV[m].toString() == ""
      //   ) {
      //     return true;
      //   }
      // }
      // return false;
    },
    evaluateCurrentMembers() {
      return 1;
    },
    //evaluate trimestral values by sum the monthly values
    evaluateTimeslots() {
      let sum_new_prev = 0;
      let sum_new_cons = 0;

      let sum_ret_prev = 0;
      let sum_ret_cons = 0;

      this.timeslotAggregations.newMembers.PREV = [0, 0, 0, 0];
      this.timeslotAggregations.retentions.PREV = [0, 0, 0, 0];
      this.timeslotAggregations.newMembers.CONS = [0, 0, 0, 0];
      this.timeslotAggregations.retentions.CONS = [0, 0, 0, 0];

      for (let i = 1; i <= 12; i++) {
        if (this.rana.newMembers.PREV && this.rana.newMembers.PREV["m" + i]) {
          sum_new_prev += (this.rana.newMembers.PREV["m" + i] || 0) * 1;
        }
        if (this.rana.newMembers.CONS && this.rana.newMembers.CONS["m" + i]) {
          sum_new_cons += (this.rana.newMembers.CONS["m" + i] || 0) * 1;
        }

        if (this.rana.retentions.PREV && this.rana.retentions.PREV["m" + i]) {
          sum_ret_prev += (this.rana.retentions.PREV["m" + i] || 0) * 1;
        }
        if (this.rana.retentions.CONS && this.rana.retentions.CONS["m" + i]) {
          sum_ret_cons += (this.rana.retentions.CONS["m" + i] || 0) * 1;
        }

        if (i % 3 == 0) {
          this.timeslotAggregations.newMembers.PREV[i / 3] = sum_new_prev;
          this.timeslotAggregations.newMembers.CONS[i / 3] = sum_new_cons;

          this.timeslotAggregations.retentions.PREV[i / 3] = sum_ret_prev;
          this.timeslotAggregations.retentions.CONS[i / 3] = sum_ret_cons;

          sum_new_prev = 0;
          sum_new_cons = 0;

          sum_ret_prev = 0;
          sum_ret_cons = 0;
        }
      }
    },

    canApprove(i, number) {
      if (number == 12) {
        i = Utils.getTimeslotFromMonth(i);
      }
      if (!this.rana) return false;
      return (
        this.role !== "ASSISTANT" &&
        (this.rana.state == "PROP" || this.rana.state == "TODO") &&
        this.rana.timeslot < "T" + i
      );
    },

    canPropose(i, number) {
      if (!this.rana) return false;
      if (number == 12) {
        i = Utils.getTimeslotFromMonth(i);
      }
      return (
        this.role === "ASSISTANT" &&
        this.rana.state == "TODO" &&
        this.rana.timeslot < "T" + i
      );
    },

    //check if a month is passed
    isPastMonth(m, timeslot) {
      let t = Utils.getTimeslotFromMonth(m);
      return "T" + t <= timeslot;
    },

    //restore backed up data
    resetProposal() {
      this.data = JSON.parse(JSON.stringify(this.ranaBackup));
      this.evaluateTimeslots();
    },

    //send rana proposal to the server
    async sendProposalOrApprovation() {
      let data = { ...this.rana };
      data["valueType"] = this.role === "ASSISTANT" ? "PROP" : "APPR";
      data["timeslot"] = this.rana.timeslot;

      let firstTimeslotMonth = Utils.getFirstTimeslotMonth(this.rana.timeslot);
      for (let k of Object.keys(data.newMembers.PREV)) {
        let index = k.substr(1, k.length) * 1;
        if (index >= firstTimeslotMonth) {
          data["n_" + k] = data.newMembers.PREV[k]
            ? data.newMembers.PREV[k]
            : null;
          data["r_" + k] = data.retentions.PREV[k]
            ? data.retentions.PREV[k]
            : null;
        }
      }
      // send only future data
      // let firstTimeslotMonth = Utils.getFirstTimeslotMonth(this.rana.timeslot);
      // for (let i = firstTimeslotMonth; i <= 12; i++) {
      //   data["m" + i] = this.rana[this.].PREV["m" + i];
      // }

      this.snackbarData.show = true;
      let result = await ApiServer.post(this.rana.id + "/rana-members", data);
      if (!result.error) {
        this.$store.commit("snackbar/setData", {
          messageLabel: "proposal_sent",
          status: "success"
        });

        this.$emit("updateRanas", result);
      } else {
        this.$store.commit("snackbar/setData", {
          messageLabel: "proposal_error",
          status: "success"
        });
      }
    },

    //check if timeslot is past
    isPastTimeslot(t, timeslot) {
      return "T" + t <= timeslot;
    },

    //calculate trimestral value each time a month changes
    calculateTimeslot(e, m, type) {
      let t = Math.ceil(m / 3);
      let startFrom = (t - 1) * 3 + 1;
      let value = 0;
      for (let i = startFrom; i < startFrom + 3; i++) {
        value += (this.rana[type].PREV["m" + i] || 0) * 1;
      }
      this.$set(this.timeslotAggregations[type].PREV, t, value);

      //let monthPrev = m == 1 ? this.rana.initialMembers : this.rana.members[m-1];
      // this.$set(
      //   this.rana.members,
      //   m,
      //   monthPrev +
      //     (this.rana.newMembers.PREV["m" + m] ? this.rana.newMembers.PREV["m" + m] : 0 -
      //       this.rana.retentions.PREV["m" + m] ? this.rana.retentions.PREV["m" + m] : 0)
      // );
    },

    //evaluate a proposal fro monthly data each time a trimestral value changes
    proposeMonths(e, t, type) {
      let value = e.target.value;
      let months = [0, 0, 0];

      if (value) {
        let unit = value > 3 ? Math.floor(value / 3) : 1;

        for (let i = 0; i < Math.min(value, 3); i++) {
          months[i] += unit;
        }

        let rest = value % 3;
        if (value > 3 && rest) {
          for (let i = 0; i < rest; i++) {
            months[i] += 1;
          }
        }

        let startFrom = (t - 1) * 3 + 1;
        for (let i = 0; i < months.length; i++) {
          let index = startFrom + i;
          this.$set(this.rana[type].PREV, "m" + index, months[i]);

          // let monthPrev = i == 0 ? this.rana.initialMembers : this.rana.members[i];

          // this.$set(
          //   this.rana.members,
          //   index,
          //   monthPrev +
          //     (this.rana.newMembers.PREV["m" + index] ? this.rana.newMembers.PREV["m" + index] : 0 -
          //       this.rana.retentions.PREV["m" + index] ? this.rana.retentions.PREV["m" + index] : 0)
          // );
        }
      }
    },

    //if i'm assistant i see proposed values, if i'm executive, area, national or admin i see approved values
    setPrevisionsByRole() {
      let prop;
      let appr;

      this.rana.newMembers.PREV = this.prevRana
        ? Object.keys(this.prevRana.newMembers.APPR).length
          ? this.prevRana.newMembers.APPR
          : this.prevRana.newMembers.PROP
        : Object.keys(this.rana.newMembers.APPR).length
        ? this.rana.newMembers.APPR
        : this.rana.newMembers.PROP;

      this.rana.retentions.PREV = this.prevRana
        ? Object.keys(this.prevRana.retentions.APPR).length
          ? this.prevRana.retentions.APPR
          : this.prevRana.retentions.PROP
        : Object.keys(this.rana.retentions.APPR).length
        ? this.rana.retentions.APPR
        : this.rana.retentions.PROP;

      if (this.prevRana) {
        this.rana.members = this.prevRana.members;
      }
    }
  },
  created() {
    setTimeout(() => {
      //backup data to allow reset
      this.ranaBackup = JSON.parse(JSON.stringify(this.data));
    });
  },
  watch: {
    rana: {
      handler: function(newVal, oldVal) {
        if (newVal) {
          this.rana = newVal;
          this.currentTimeslot = this.rana.timeslot;
          this.role = this.$store.getters["getUser"];
          if (
            this.role === "EXECUTIVE" ||
            (this.role === "ADMIN" && this.rana.state === "TODO")
          ) {
            this.editable = true;
          }
          this.setPrevisionsByRole();
          this.evaluateTimeslots();
        }
      },
      immediate: true,
      deep: true
    }
  }
};
</script>
<style lang="scss">
@import "../assets/variables.scss";

#rana {
  .v-input {
    padding: 0;
    margin: 0;
  }
  td {
    border: 1px solid lightgray;
  }
  .v-input__control {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .v-text-field__details {
    display: none;
  }
  .v-input__slot {
    margin: 0;
    &:before {
      display: none;
    }
  }

  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
  /* Firefox */
  input[type="number"] {
    -moz-appearance: textfield;
  }
  input {
    text-align: center;
  }
  .background-green {
    background: lighten($lightgreen, 30%);
  }
  .background-yellow {
    background: lighten($lightyellow, 25%);
  }
  .background-red {
    background: lighten($lightred, 35%);
  }
  .background-grey {
    background: lighten($lightgrey, 65%);
  }
  .stroke-green {
    border: 2px solid $lightgreen !important;
  }
  tr:hover {
    background: transparent;
  }
  .circle {
    width: 15px;
    height: 15px;
    margin-right: 5px;
    border-radius: 50%;
    border: 1px solid $lightgrey;
  }
  .v-data-table-header-mobile {
    display: none;
  }
}
</style>
