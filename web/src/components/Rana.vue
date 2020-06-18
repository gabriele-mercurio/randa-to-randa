<template>
  <v-data-table disable-pagination hide-default-footer id="rana">
    <template v-slot:header>
      <thead>
        <tr>
          <th colspan="3" class="text-center">T1</th>
          <th colspan="3" class="text-center">T2</th>
          <th colspan="3" class="text-center">T3</th>
          <th colspan="3" class="text-center">T4</th>
        </tr>
      </thead>
    </template>
    <template v-slot:body>
      <tbody>
        <tr>
          <td colspan="3">
              <div class="d-flex">
                <v-text-field
                @keydown="proposeMonths($event, 1)"
                :disabled="pastTimeslot('T1')"
                type="number"
                />
                <span>{{data.cons.t1}}</span>

            </div>
          </td>
          <td colspan="3" class="text-center">
            <v-text-field
              @keydown="proposeMonths($event, 2)"
              :disabled="pastTimeslot('T2')"
              type="number"
            />
          </td>
          <td colspan="3" class="text-center">
            <v-text-field
              @keydown="proposeMonths($event, 3)"
              :disabled="pastTimeslot('T3')"
              type="number"
            />
          </td>
          <td colspan="3" class="text-center">
            <v-text-field
              @keydown="proposeMonths($event, 4)"
              :disabled="pastTimeslot('T4')"
              type="number"
            />
          </td>
        </tr>
        <tr>
          <td class="text-center">
              <span>M1</span>
            <v-text-field
              placeholder="M1"
              :disabled="pastMonth('1')"
              type="number"
              v-model="data.appr.months[0]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M2"
              :disabled="pastMonth('2')"
              type="number"
              v-model="data.appr.months[1]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M3"
              :disabled="pastMonth('3')"
              type="number"
              v-model="data.appr.months[2]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M4"
              :disabled="pastMonth('4')"
              type="number"
              v-model="data.appr.months[3]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M5"
              :disabled="pastMonth('5')"
              type="number"
              v-model="data.appr.months[4]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M6"
              :disabled="pastMonth('6')"
              type="number"
              v-model="data.appr.months[5]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M7"
              :disabled="pastMonth('7')"
              type="number"
              v-model="data.appr.months[6]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M8"
              :disabled="pastMonth('8')"
              type="number"
              v-model="data.appr.months[7]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M9"
              :disabled="pastMonth('9')"
              type="number"
              v-model="data.appr.months[8]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M10"
              :disabled="pastMonth('10')"
              type="number"
              v-model="data.appr.months[9]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M11"
              :disabled="pastMonth('11')"
              type="number"
              v-model="data.appr.months[10]"
            />
          </td>
          <td class="text-center">
            <v-text-field
              placeholder="M12"
              :disabled="pastMonth('12')"
              type="number"
              v-model="data.appr.months['p']"
            />
          </td>
        </tr>
      </tbody>
    </template>
    <template slot="no-data">
      <tr style="display: none;">
        ciao
      </tr>
    </template>
  </v-data-table>
</template>
<script>
import Utils from "../services/Utils";

export default {
  data() {
    return {
      currentTimeslot: null,
      data: {
          appr: {
              t: [
                  0,1,2,3
              ],
              months: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
          },
          cons: {
              t: [
                  0,1,2,3
              ],
              months: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
          }
      }
    };
  },
  props: {
    defData: []
  },
  methods: {
    pastMonth(m) {
      return m <= new Date().getMonth() + 1;
    },
    pastTimeslot(t) {
      return t <= this.currentTimeslot;
    },
    proposeMonths(e, t) {
      let value = e.key;
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
          console.log(i);
          console.log(startFrom + i);
          this.data.months[startFrom + i] = { ...months[i] };
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
  th:first-of-type, td:first-of-type {
    border-left: 1px solid rgba(0, 0, 0, 0.12);
  }
  tr:last-of-type {
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  }
}
</style>
