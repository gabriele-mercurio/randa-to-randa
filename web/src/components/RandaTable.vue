<template>
  <div id="randaTable">
    <v-data-table
      disable-pagination
      hide-default-footer
      class="elevation-2 mb-8"
      v-if="chapters"
      :id="id ? id : ''"
    >
      <template v-slot:body>
        <thead>
          <tr>
            <th>
              <h2>{{ getRandaType() }}</h2>
            </th>
            <th></th>
            <th class="text-center" v-if="initial">Iniziali</th>
            <th class="text-center">T1</th>
            <th class="text-center">T2</th>
            <th class="text-center">T3</th>
            <th class="text-center">T4</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="chapter in chapters" :key="chapter.name">
            <td>{{ chapter.name }}</td>
            <td></td>
            <th class="text-center" v-if="initial">{{ chapter.initial }}</th>
            <td class="text-center">{{ chapter.data[0] }}</td>
            <td class="text-center">{{ chapter.data[1] }}</td>
            <td class="text-center">{{ chapter.data[2] }}</td>
            <td class="text-center">{{ chapter.data[3] }}</td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <th class="text-center" v-if="initial">{{ totalInitialMembers }}</th>
            <td class="text-center">{{ totals[0] }}</td>
            <td class="text-center">{{ totals[1] }}</td>
            <td class="text-center">{{ totals[2] }}</td>
            <td class="text-center">{{ totals[3] }}</td>
          </tr>

          <!-- <tr>
            <td v-for="i in (0, 4)" :key="i" class="text-center">
              {{ evaluateTotal(i) }}
            </td>
          </tr> -->
        </tbody>
      </template>
    </v-data-table>

    <v-data-table
      disable-pagination
      hide-default-footer
      class="elevation-2 mb-8"
      v-if="plainData"
    >
      <template v-slot:body>
        <thead>
          <tr>
            <th>
              <h2>{{ getRandaType() }}</h2>
            </th>
            <th></th>
            <th class="text-center">T1</th>
            <th class="text-center">T2</th>
            <th class="text-center">T3</th>
            <th class="text-center">T4</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td></td>
            <td></td>
            <td class="text-center width-100">{{ plainData[0] }}</td>
            <td class="text-center width-100">{{ plainData[1] }}</td>
            <td class="text-center width-100">{{ plainData[2] }}</td>
            <td class="text-center width-100">{{ plainData[3] }}</td>
          </tr>
        </tbody>
      </template>
    </v-data-table>

    <v-data-table
      disable-pagination
      hide-default-footer
      class="elevation-2 mb-8"
      v-if="singleValue"
    >
      <template v-slot:body>
        <thead>
          <tr>
            <th>
              <h2>{{ getRandaType() }}</h2>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">{{ singleValue }}</td>
          </tr>
        </tbody>
      </template>
    </v-data-table>
  </div>
</template>
<script>
export default {
  props: {
    chapters: {},
    plainData: {},
    randaType: {},
    singleValue: {},
    initial: {
      default: false
    },
    plainArray: {
      default: false
    },
    id: {
      default: ''
    }
  },
  data() {
    return {
      printableData: null,
      totals: [0, 0, 0, 0],
      totalInitialMembers: 0
    };
  },
  methods: {
    getRandaType() {
      switch (this.randaType) {
        case "chapters_act":
          return "Attivi";
        case "chapters_new":
          return "Nuovi membri";
        case "chapters_ret":
          return "Uscite";
        case "chapters_average":
          return "Media capitoli";
        case "core_groups_act":
          return "Core groups";
        case "core_groups_new":
          return "";
        case "core_groups_ret":
          return "";
        case "directors":
          return "Directors";
        case "num_chapters":
          return "Numero capitoli";
        case "note":
          return "Nota";
      }
    },
    getTotal(chapter) {
      let sum = 0;
      for (let i = 0; i < chapter.data.length; i++) {
        sum += chapter.data[i] ? chapter.data[i] : 0;
      }
      return sum;
    },
    getPlainTotal(element) {
      let sum = 0;
      for (let i = 0; i < element.length; i++) {
        sum += element[i] ? element[i] * 1 : 0;
      }
      return sum;
    }
  },
  watch: {
    chapters: {
      handler: function(newVal, oldVal) {
        if (newVal) {
          for (let chapter of Object.keys(newVal)) {
            let c = newVal[chapter];
            for (let i = 0; i < 4; i++) {
              this.totals[i] += c.data[i];
            }
            this.totalInitialMembers += c.initial;
          }
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
#randaTable {
  .total {
    background: rgba(0, 0, 0, 0.1);
    width: 70px;
  }
  thead {
    th {
      background-color: lighten($lightred, 35%);
      font-weight: bold !important;
    }
  }
  .width-100 {
    width: 100px;
  }
}
</style>
