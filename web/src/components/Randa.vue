<template>
  <div id="randa_" v-if="randa" class="px-6">
    <div class="pt-6" v-if="layout !== 'split'">
      <h3>
        {{ title }}
        <small>{{ randa.timeslot }} {{ randa.year }}</small>
      </h3>
      <div class="d-flex flex-column align-start my-4">
        <div class="d-flex flex-row align-center">
          <div class="circle background-lightred"></div>
          <div class="ml-1">Core group</div>
        </div>
        <div class="d-flex flex-row align-center">
          <div class="circle background-red"></div>
          <div class="ml-1">Chapter</div>
        </div>
        <div class="d-flex flex-row align-center">
          <div>
            <v-icon style="font-size:16px">mdi-diameter-variant</v-icon>
          </div>
          <div class="ml-1">Sospeso</div>
        </div>
        <div class="d-flex flex-row align-center">
          <div>
            <v-icon style="font-size:16px">mdi-close</v-icon>
          </div>
          <div class="ml-1">Chiuso</div>
        </div>
        <div class="d-flex flex-row align-center">
          <div class="circle background-green"></div>
          <div class="ml-1">Approvato</div>
        </div>
      </div>
    </div>

    <v-data-table disable-pagination hide-default-footer v-if="layout !== 'split'">
      <template v-slot:body>
        <tbody>
          <tr>
            <th></th>
            <th></th>
            <th class="text-center" colspan="4">T1</th>
            <th class="text-center" colspan="4">T2</th>
            <th class="text-center" colspan="4">T3</th>
            <th class="text-center" colspan="4">T4</th>
            <th class="text-center" colspan="4" v-if="showTotal">Totali</th>
          </tr>
          <tr>
            <th class="text-center bordered">Capitolo</th>
            <th class="text-center bordered">Iniziali</th>
            <th class="text-center small-col"></th>
            <th class="text-center">Entrate</th>
            <th class="text-center">Uscite</th>
            <th class="text-center bordered">Totali</th>
            <th class="text-center small-col"></th>
            <th class="text-center">Entrate</th>
            <th class="text-center">Uscite</th>
            <th class="text-center bordered">Totali</th>
            <th class="text-center"></th>
            <th class="text-center">Entrate</th>
            <th class="text-center">Uscite</th>
            <th class="text-center bordered">Totali</th>
            <th class="text-center"></th>
            <th class="text-center">Entrate</th>
            <th class="text-center">Uscite</th>
            <th class="text-center bordered">Totali</th>
          </tr>
          <tr
            v-for="chapter in randa.chapters"
            :key="chapter.chapter"
            :class="chapter.approved ? 'approved' : ''"
          >
            <td class="text-center bordered">{{ chapter.chapter }}</td>
            <td class="text-center bordered">{{ chapter.initialMembers }}</td>
            <td
              class="text-center"
              :class="chapter.chapter_history ? chapter.chapter_history[0] : ''"
            >
              <span class="circle">
                <v-icon
                  v-if="
                    chapter.chapter_history &&
                      chapter.chapter_history[0] === 'SUSPENDED'
                  "
                >mdi-diameter-variant</v-icon>

                <v-icon
                  v-if="
                    chapter.chapter_history &&
                      chapter.chapter_history[0] === 'CLOSED'
                  "
                >mdi-close</v-icon>
              </span>
            </td>
            <td class="text-center">{{ chapter.newMembers[0] }}</td>
            <td class="text-center">{{ chapter.retentions[0] }}</td>
            <td class="text-center bordered">{{ chapter.members[0] }}</td>

            <td
              class="text-center"
              :class="chapter.chapter_history ? chapter.chapter_history[1] : ''"
            >
              <span class="circle">
                <v-icon
                  v-if="
                    chapter.chapter_history &&
                      chapter.chapter_history[1] === 'SUSPENDED'
                  "
                >mdi-diameter-variant</v-icon>

                <v-icon
                  v-if="
                    chapter.chapter_history &&
                      chapter.chapter_history[1] === 'CLOSED'
                  "
                >mdi-close</v-icon>
              </span>
            </td>
            <td class="text-center">{{ chapter.newMembers[1] }}</td>
            <td class="text-center">{{ chapter.retentions[1] }}</td>
            <td class="text-center bordered">{{ chapter.members[1] }}</td>

            <td
              class="text-center"
              :class="chapter.chapter_history ? chapter.chapter_history[2] : ''"
            >
              <span class="circle">
                <v-icon
                  v-if="
                    chapter.chapter_history &&
                      chapter.chapter_history[2] === 'SUSPENDED'
                  "
                >mdi-diameter-variant</v-icon>

                <v-icon
                  v-if="
                    chapter.chapter_history &&
                      chapter.chapter_history[1] === 'CLOSED'
                  "
                >mdi-close</v-icon>
              </span>
            </td>
            <td class="text-center">{{ chapter.newMembers[2] }}</td>
            <td class="text-center">{{ chapter.retentions[2] }}</td>
            <td class="text-center bordered">{{ chapter.members[2] }}</td>

            <td
              class="text-center"
              :class="chapter.chapter_history ? chapter.chapter_history[3] : ''"
            >
              <span class="circle">
                <v-icon
                  v-if="
                    chapter.chapter_history &&
                      chapter.chapter_history[3] === 'SUSPENDED'
                  "
                >mdi-diameter-variant</v-icon>

                <v-icon
                  v-if="
                    chapter.chapter_history &&
                      chapter.chapter_history[3] === 'CLOSED'
                  "
                >mdi-close</v-icon>
              </span>
            </td>
            <td class="text-center">{{ chapter.newMembers[3] }}</td>
            <td class="text-center">{{ chapter.retentions[3] }}</td>
            <td class="text-center bordered">{{ chapter.members[3] }}</td>
            <td v-if="showTotal" class="text-center">{{ evaluateTotal(chapter) }}</td>
          </tr>
          <tr class="font-weight-black">
            <td>Totale</td>
            <td>{{ totals["initial"] }}</td>
            <td></td>
            <td class="text-center">{{ totals["new"][0] }}</td>
            <td class="text-center">{{ totals["ret"][0] }}</td>
            <td class="text-center bordered">{{ totals["act"][0] }}</td>

            <td></td>
            <td class="text-center">{{ totals["new"][1] }}</td>
            <td class="text-center">{{ totals["ret"][1] }}</td>
            <td class="text-center bordered">{{ totals["act"][1] }}</td>

            <td></td>
            <td class="text-center">{{ totals["new"][2] }}</td>
            <td class="text-center">{{ totals["ret"][2] }}</td>
            <td class="text-center bordered">{{ totals["act"][2] }}</td>
            <td></td>
            <td class="text-center">{{ totals["new"][3] }}</td>
            <td class="text-center">{{ totals["ret"][3] }}</td>
            <td class="text-center bordered">{{ totals["act"][3] }}</td>

            <td class="text-center" v-if="showTotal">
              {{
              totals["new"][0] +
              totals["ret"][0] +
              totals["act"][0] +
              totals["new"][1] +
              totals["ret"][1] +
              totals["act"][1] +
              totals["new"][2] +
              totals["ret"][2] +
              totals["act"][2] +
              totals["new"][3] +
              totals["ret"][3] +
              totals["act"][3]
              }}
            </td>
          </tr>
        </tbody>
      </template>
    </v-data-table>

    <template v-else>
      <table id="table_wrapper" class="invisible">
        <table class="invisible">
          <tbody>
            <tr>
              <td colspan="6" class="text-cetner">
                <span class="font-italic font-weight-bold">
                  {{
                  randa.region
                  }}
                </span>
              </td>
            </tr>
            <tr>
              <td colspan="6" class="text-cetner">Randa {{ randa.year }} {{ randa.timeslot }}</td>
            </tr>
          </tbody>
        </table>
        <RandaTable
          :chapters="randa.chapters_ret"
          :randaType="'chapters_ret'"
          :id="'chapters_ret'"
        />
        <RandaTable :plainData.sync="avg" :randaType="'chapters_average'" :id="'chapters_average'" />
        <RandaTable
          :plainData="randa.num_chapters"
          :randaType="'num_chapters'"
          :id="'num_chapters'"
        />
        <RandaTable
          :chapters="randa.chapters_new"
          :randaType="'chapters_new'"
          :id="'chapters_new'"
        />
        <RandaTable :plainData="randa.directors" :randaType="'directors'" />
        <RandaTable :singleValue="randa.note" :randaType="'note'" />
        <RandaTable
          :chapters="randa.chapters_act"
          :randaType="'chapters_act'"
          :id="'chapters_act'"
          :initial="true"
        />
        <RandaTable :chapters="randa.core_groups_act" :randaType="'core_groups_act'" />
      </table>
    </template>
  </div>
</template>
<script>
import Utils from "../services/Utils";
import ApiServer from "../services/ApiServer";
import RandaTable from "../components/RandaTable";

export default {
  components: {
    RandaTable
  },
  data() {
    return {
      totals: null,
      avg: [],
      regionName: null
    };
  },
  created() {
    if (this.title === "Randa dream") {
    }
    let totals = {
      initial: 0,
      new: [0, 0, 0, 0],
      ret: [0, 0, 0, 0],
      act: [0, 0, 0, 0]
    };
    if (this.randa && this.randa.chapters) {
      this.randa.chapters.forEach(chapter => {
        totals["initial"] += chapter.initialMembers;
        for (let i = 0; i < 4; i++) {
          totals["new"][i] += chapter["newMembers"][i]
            ? chapter["newMembers"][i]
            : 0;
          totals["ret"][i] += chapter["retentions"][i]
            ? chapter["retentions"][i]
            : 0;
          totals["act"][i] += chapter["members"][i] ? chapter["members"][i] : 0;
        }
      });
    }

    setTimeout(() => {
      this.regionName = this.$store.getters["getRegion"].name;
      debugger;
    }, 4000);

    this.totals = totals;
  },
  props: {
    title: {
      type: String,
      default: null
    },
    randa: {
      default: null
    },
    layout: {
      default: "",
      type: String
    },
    showTotal: {
      default: true
    }
  },
  methods: {
    evaluateTotal(chapter) {
      let sum = chapter.initialMembers;
      for (let i = 0; i < 4; i++) {
        sum += chapter.newMembers[i]
          ? chapter.newMembers[i]
          : 0 - chapter.retentions[i]
          ? chapter.retentions[i]
          : 0;
      }
      return sum;
    }
  },
  watch: {
    randa: {
      handler: function(newVal, oldVal) {
        if (newVal && newVal.chapters_act) {
          let sum = [0, 0, 0, 0];
          let count = [0, 0, 0, 0];
          for (let k in newVal.chapters_act) {
            let element = newVal.chapters_act[k];
            if (element.data[0] !== null) {
              count[0]++;
            }
            sum[0] += element.data[0];

            if (element.data[1] !== null) {
              count[1]++;
            }
            sum[1] += element.data[1];

            if (element.data[2] !== null) {
              count[2]++;
            }
            sum[2] += element.data[2];

            if (element.data[3] !== null) {
              count[3]++;
            }
            sum[3] += element.data[3];
          }
          let avg = [];
          avg[0] = Math.round((sum[0] / count[0]) * 100) / 100;
          avg[1] = Math.round((sum[1] / count[1]) * 100) / 100;
          avg[2] = Math.round((sum[2] / count[2]) * 100) / 100;
          avg[3] = Math.round((sum[3] / count[3]) * 100) / 100;

          this.avg = avg;
        }
      },
      deep: true,
      immediate: true
    }
  }
};
</script>
<style lang="scss">
@import "../assets/variables.scss";

#randa_ {
  table {
    border: 1px solid lightgray;
    td,
    th {
      height: 30px;
    }
  }
  .bordered {
    border-right: 2px solid lightgray;
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
    background: $lightred;
  }
  .background-lightred {
    background: lighten($lightred, 25%);
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
    width: 15px !important;
    height: 15px !important;
    margin-right: 5px;
    border-radius: 50%;
    margin: 0 auto;
    display: block;
  }
  .v-data-table-header-mobile {
    display: none;
  }

  .CORE_GROUP {
    .circle {
      background: lighten($lightred, 25%);
    }
  }
  .CHAPTER {
    .circle {
      background: $lightred;
    }
  }

  .SUSPEND,
  .CLOSED,
  .CHAPTER,
  .CORE_GROUP {
    width: 20px !important;
  }

  tr.approved {
    td {
      background: lighten($lightgreen, 30%);
      padding: 5px;
    }
  }
  table.invisible {
    border-style: none;
  }
  table.hidden {
    display: none;
  }
  #table_wrapper {
    width: 100%;
  }

  @media print {
    td,
    th {
      height: 20px;
    }
  }
}
</style>
