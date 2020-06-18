<template>
  <div id="rana">
    <div class="mb-3">
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
                  :cols="pastTimeslot(i) ? '6' : 12"
                  class="pa-0 d-flex justify-center align-center"
                  :class="{ 'border-right background-yellow': pastTimeslot(i) }"
                >
                  <v-text-field
                    :placeholder="'T' + i"
                    :disabled="pastTimeslot(i)"
                    type="number"
                    :class="{ bordered: !pastTimeslot(i) }"
                    @keyup="proposeMonths($event, i)"
                    v-model="data.appr.t[i]"
                  />
                </v-col>
                <v-col
                  v-if="pastTimeslot(i)"
                  cols="6"
                  class="pa-0 background-green font-italic font-weight-light disabled d-flex justify-center align-center"
                  >{{ data.cons.t[i] }}</v-col
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
                  :cols="pastMonth(i) ? '6' : 12"
                  class="pa-0 d-flex justify-center align-center"
                  :class="{ 'border-righ background-yellow': pastMonth(i) }"
                >
                  <v-text-field
                    :placeholder="'M' + i"
                    :disabled="pastMonth(i)"
                    type="number"
                    v-model="data.appr.m[i]"
                    @keyup="calculateTimeslot($event, i)"
                    :class="{ bordered: !pastMonth(i) }"
                  />
                </v-col>
                <v-col
                  v-if="pastMonth(i)"
                  cols="6"
                  class="pa-0 background-green font-italic font-weight-light disabled d-flex justify-center align-center"
                  >{{ data.cons.m[i] }}</v-col
                >
              </v-row>
            </td>
          </tr>
        </tbody>
      </template>
      <template slot="no-data">
        <tr style="display: none;"></tr>
      </template>
      <template slot="footer">
        <div class="d-flex justify-end mt-4">
          <v-btn
            type="submit"
            normal
            color="primary"
            @click="sendProposal()"
          >
            {{ $t("send_proposal") }}
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
      currentTimeslot: null,
      data: {
        appr: {
          t: [0, 1, 2, 3],
          m: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        },
        cons: {
          t: [0, 1, 2, 3],
          m: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        }
      }
    };
  },
  props: {
    defData: [],
    rana: null
  },
  methods: {
    pastMonth(m) {
      return m <= new Date().getMonth() + 1;
    },
    async sendProposal() {
        debugger;
      let data = {
        valueType: "PROP",
        timeslot: this.currentTimeslot
      };
      for (let i = 0; i < 12; i++) {
        data["m" + (i+1)] = this.data.appr.m[i];
      }
      console.log(data);
      debugger;
      let result = await ApiServer.post(this.rana.id + "/rana-renewed", data);
    },
    pastTimeslot(t) {
      return t <= this.currentTimeslot;
    },
    calculateTimeslot(e, m) {
      let t = Math.ceil(m / 3);
      let startFrom = (t - 1) * 3 + 1;
      let value = 0;
      for (let i = startFrom; i <= startFrom + 3; i++) {
        value += this.data.appr.m[i] * 1;
      }
      this.$set(this.data.appr.t, t, value);
    },
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

        let startFrom = (t - 1) * 3;
        for (let i = 0; i < months.length; i++) {
          this.$set(this.data.appr.m, startFrom + i, months[i]);
        }
      }
    }
  },
  created() {
    this.currentTimeslot = Utils.getCurrentTimeslot();
    if (this.defData) {
      this.data = this.defData;
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
  }
  .circle {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1px solid $lightgrey;
  }
}
</style>
