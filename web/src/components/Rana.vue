<template>
  <div id="rana">
    <h3 v-if="!editable">
      Periodo:
      <span class="font-italic font-weight-light">{{ currentTimeslot }}</span>
    </h3>
    <h3 v-else>
      Rana per:
      <span class="font-italic font-weight-light">{{ nextTimeslot }}</span>
    </h3>
    <div class="mb-3" v-if="editable">
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
    <v-data-table disable-pagination hide-default-footer>
      <template v-slot:body>
        <tbody>
          <tr>
            <td
              colspan="3"
              v-for="i in (0, 4)"
              :key="i"
              class="text-center pa-0 border-right-bold border-top-bold"
            >
              <div class="border-bottom background-grey pa-1">T{{ i }}</div>
              <v-row class="pa-0 ma-0">
                <v-col
                  :cols="isPastTimeslot(i) ? '6' : 12"
                  class="pa-0 d-flex justify-center align-center"
                  :class="{
                    'border-right': isPastTimeslot(i),
                    'background-yellow': isPastTimeslot(i) || !editable
                  }"
                >
                  <v-text-field
                    :placeholder="'T' + i"
                    :disabled="isPastTimeslot(i) || !editable || (!canApprove() && !canPropose())"
                    type="number"
                    :class="{ bordered: !isPastTimeslot(i) && editable }"
                    @keyup="proposeMonths($event, i)"
                    v-model="timeslotAggregations.PREV[i]"
                  />
                </v-col>
                <v-col
                  v-if="isPastTimeslot(i)"
                  cols="6"
                  class="pa-0 background-green font-italic font-weight-light disabled d-flex justify-center align-center"
                  >{{ timeslotAggregations.CONS[i] }}</v-col
                >
              </v-row>
            </td>
          </tr>
          <tr>
            <td
              v-for="i in (0, 12)"
              :key="i"
              class="text-center pa-0 border-top-none border-right-semibold border-bottom-bold"
              :class="{ 'border-right-bold': i % 3 == 0 }"
            >
              <div class="border-bottom background-grey pa-1">M{{ i }}</div>
              <v-row class="pa-0 ma-0">
                <v-col
                  :cols="isPastMonth(i) ? '6' : 12"
                  class="pa-0 d-flex justify-center align-center"
                  :class="{
                    'border-righ': isPastMonth(i),
                    'background-yellow': isPastMonth(i) || !editable
                  }"
                >
                  <v-text-field
                    :placeholder="'M' + i"
                    :disabled="isPastMonth(i) || !editable || (!canApprove() && !canPropose())"
                    type="number"
                    v-model="data.PREV['m' + i]"
                    @keyup="calculateTimeslot($event, i)"
                    :class="{ bordered: !isPastMonth(i) && editable }"
                  />
                </v-col>
                <v-col
                  v-if="isPastMonth(i)"
                  cols="6"
                  class="pa-0 background-green font-italic font-weight-light disabled d-flex justify-center align-center"
                  >{{ data.CONS["m" + i] }}
                </v-col>
              </v-row>
            </td>
          </tr>
        </tbody>
      </template>
      <template slot="no-data">
        <tr style="display: none;"></tr>
      </template>
      <template slot="footer" v-if="editable">
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
            :disabled="!canApprove() && !canPropose()"
            @click="sendProposalOrApprovation()"
          >
            <span v-if="role === 'ASSISTANT'">{{ $t("send_proposal") }}</span>
            <span v-else>{{ $t("approve_proposal") }}</span>
          </v-btn>
        </div>
      </template>
    </v-data-table>
  </div>
</template>
<script>
import Utils from "../services/Utils";
import ApiServer from "../services/ApiServer";

export default {
  data() {
    return {
      timeslotAggregations: {
        PREV: [],
        CONS: []
      },
      data: {
        PREV: [],
        CONS: []
      },
      ranaBackup: null
    };
  },
  props: {
    defData: [],
    rana: null,
    currentTimeslot: null,
    editable: false,
    ranaType: null
  },
  computed: {
    nextTimeslot() {
      return "T" + (Utils.getNumericTimeslot() + 1);
    },
    role() {
      if (this.$store.getters["getRegion"]) {
        return this.$store.getters["getRegion"].role;
      }
    }
  },
  methods: {
    //evaluate trimestal values by sum the monthly values
    evaluateTimeslots() {
      let sum_prev = 0;
      let sum_cons = 0;
      this.timeslotAggregations.PREV = [0,0,0,0];
      this.timeslotAggregations.CONS = [0,0,0,0];

      for (let i = 1; i <= 12; i++) {
        if(this.data.PREV && this.data.PREV["m" + i]) {
          sum_prev += (this.data.PREV["m" + i] || 0) * 1;
        }
        if(this.data.CONS && this.data.CONS["m" + i]) {
          sum_cons += (this.data.CONS["m" + i] || 0) * 1;
        }
        if (i % 3 == 0) {
          this.timeslotAggregations.PREV[i/3] = sum_prev;
          this.timeslotAggregations.CONS[i/3] = sum_cons;
          sum_prev = 0;
          sum_cons = 0;
        }
      }


    },

    canApprove() {
      return this.role === "EXECUTIVE" && this.rana.state == "PROPOSED";
    },

    canPropose() {
      return this.role === "ASSISANT" && this.rana.state == "TODO";
    },

    //check if a month is passed
    isPastMonth(m) {
      if (!this.currentTimeslot) return false;
      let t = Utils.getTimeslotFromMonth(m);
      return "T" + t <= this.currentTimeslot;
    },

    //restore backed up data
    resetProposal() {
      this.data = JSON.parse(JSON.stringify(this.ranaBackup));
      this.evaluateTimeslots();
    },

    //send rana proposal to the server
    async sendProposalOrApprovation() {
      let data = {
        valueType: this.role === "ASSISTANT" ? "PROP" : "APPR",
        timeslot: this.currentTimeslot
      };

      // send only future data
      let firstTimeslotMonth = Utils.getFirstTimeslotMonth(this.nextTimeslot);
      for (let i = firstTimeslotMonth; i <= 12; i++) {
        data["m" + i] = this.data.PREV["m" + i];
      }
      let result = await ApiServer.post(this.rana.id + "/rana-renewed", data);
    },

    //check if timeslot is past
    isPastTimeslot(t) {
      if (!this.currentTimeslot) return false;
      return "T" + t <= this.currentTimeslot;
    },

    //calculate trimestral value each time a month changes
    calculateTimeslot(e, m) {
      let t = Math.ceil(m / 3);
      let startFrom = (t - 1) * 3 + 1;
      let value = 0;
      for (let i = startFrom; i < startFrom + 3; i++) {
        value += (this.data.PREV["m" + i] || 0) * 1;
      }
      this.$set(this.timeslotAggregations.PREV, t, value);
    },

    //evaluate a proposal fro monthly data each time a trimestral value changes
    proposeMonths(e, t) {
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
          this.$set(this.data.PREV, "m" + (startFrom + i), months[i]);
        }
      }
    },

    //if i'm assistant i see proposed values, if i'm executive, area, national or admin i see approved values
    setPrevisionsByRole() {
      if (this.role === "ASSISTANT") {
        this.data.PREV = this.data.PROP;
      } else {
        this.data.PREV = this.data.APPR;
        debugger;
        let firstMonthToApprove = Utils.getFirstTimeslotMonth(this.currentTimeslot);
        for(let i = firstMonthToApprove; i <= 12; i++) {
          this.data.PREV["m" + i] = this.data.PROP["m" + i];
        }
        debugger;
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
        this.data = newVal[this.ranaType];
        this.setPrevisionsByRole();
        this.evaluateTimeslots();
      }
    }
  }
};
</script>
<style lang="scss">
@import "../assets/variables.scss";

#rana {
  td,
  th {
    border-right: 1px solid rgba(0, 0, 0, 0.12);
    border-top: 1px solid rgba(0, 0, 0, 0.12);
  }
  thead tr:last-of-type,
  tbody tr:last-of-type {
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  }
  th:first-of-type,
  td:first-of-type {
    border-left: 2px solid rgba(0, 0, 0, 0.4) !important;
  }
  tr:last-of-type {
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  }
  .border-bottom {
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  }
  .border-top-none {
    border-top-style: none;
  }
  .border-right {
    border-right: 1px solid rgba(0, 0, 0, 0.2);
  }
  .border-right-bold {
    border-right: 2px solid rgba(0, 0, 0, 0.4) !important;
    &:last-of-type {
      border-right: none;
    }
  }
  .border-top-bold {
    border-top: 2px solid rgba(0, 0, 0, 0.4) !important;
  }
  .border-bottom-bold {
    border-bottom: 2px solid rgba(0, 0, 0, 0.4) !important;
  }
  .border-right-semibold {
    border-right: 1px solid rgba(0, 0, 0, 0.3);
  }
  .v-input {
    padding: 0;
    margin: 0;
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
    background: lighten($lightgrey, 70%);
  }
  .stroke-green {
    border: 2px solid $lightgreen !important;
  }
  tr:hover {
    background: transparent;
  }
  .bordered {
    border: 2px solid $lightgreen;
    box-sizing: border-box !important;
    margin: -2px;
    &.v-input--is-disabled {
      border-style: none!important;
    }
  }
  .circle {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1px solid $lightgrey;
  }
  .v-data-table-header-mobile {
    display: none;
  }
}
</style>
