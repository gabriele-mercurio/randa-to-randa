<template>
  <div id="randaTable">
    <v-data-table
      disable-pagination
      hide-default-footer
      class="elevation-2 mx-6 mb-8 "
      v-if="chapters"
    >
      <template v-slot:body>
        <thead>
          <tr>
            <th>
              <h2>{{ getRandaType() }}</h2>
            </th>
            <th></th>
            <th class="text-center">T0</th>
            <th class="text-center">T1</th>
            <th class="text-center">T2</th>
            <th class="text-center">T3</th>
            <th class="text-center total">Totale</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="chapter in chapters" :key="chapter.name">
            <td>{{ chapter.name }}</td>
            <td></td>
            <td class="text-center">{{ chapter.data[0] }}</td>
            <td class="text-center">{{ chapter.data[1] }}</td>
            <td class="text-center">{{ chapter.data[2] }}</td>
            <td class="text-center">{{ chapter.data[3] }}</td>
            <td class="text-center total">{{ getTotal(chapter) }}</td>
          </tr>
        </tbody>
      </template>
    </v-data-table>

    <v-data-table
      disable-pagination
      hide-default-footer
      class="elevation-2  mx-6 mb-8"
      v-if="plainData"
    >
      <template v-slot:body>
        <thead>
          <tr>
            <th>
              <h2>{{ getRandaType() }}</h2>
            </th>
            <th></th>
            <th class="text-center">T0</th>
            <th class="text-center">T1</th>
            <th class="text-center">T2</th>
            <th class="text-center">T3</th>
            <th class="text-center total">Totale</th>
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
            <td class="text-center width-100 total">{{ getPlainTotal(plainData) }}</td>
          </tr>
        </tbody>
      </template>
    </v-data-table>

    <v-data-table
      disable-pagination
      hide-default-footer
      class="elevation-2  mx-6 mb-8"
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
    plainArray: {
      default: false
    }
  },
  data() {
    return {
      printableData: null
    };
  },
  methods: {
    getRandaType() {
      switch (this.randaType) {
        case "chapters_act":
          return "Attivi";
        case "chapters_new":
          return "Nuovi memberi";
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
      font-weight: bold!important;
    }
  }
  .width-100 {
      width: 100px;
  }
}
</style>
