<template>
  <v-data-table
    :headers="headers"
    :items="directors"
    disable-pagination
    hide-default-footer
    class="elevation-3"
  >
  <template v-slot:item.fullName="{ item }">
  <div class="d-flex flex-column">
    {{item.fullName}}
    <small font-italic font-weight-light v-if="item.freeAccount">
      (account gratuito)
    </small>
  </div>
  </template>
    <template v-slot:item.actions="{ item }">
      <v-menu bottom left>
        <template v-slot:activator="{ on }">
          <v-btn dark icon v-on="on">
            <v-icon class="primary--text">mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item @click="edit(item)">
            <v-list-item-title>Modifica director</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </template>
  </v-data-table>
</template>
<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";
export default {
  components: {},
  props: {
    directors: {
      type: Array,
      default: null
    }
  },
  data() {
    return {
      headers: [
        { text: "Nome", value: "fullName" },
        { text: "Email", value: "email" },
        { text: "Ruolo", value: "role" },
        { text: "Area dir.", value: "supervisor.fullName" },
        { text: "Tipo di paga", value: "payType" },
        { text: "% fissa", value: "fixedPercentage" },
        { text: "% lancio", value: "launchPercentage" },
        { text: "Green light", value: "greenLightPercentage" },
        { text: "Yellow light", value: "yellowLightPercentage" },
        { text: "Red light", value: "redLightPercentage" },
        { text: "Grey light", value: "greyLightPercentage" },
        { value: "actions" }
      ]
    };
  },
  methods: {
    edit(item) {
      this.$emit("edit", item);
    },
  },
  watch: {
    directors: {
      handler: function(oldVal, newVal) {
      }
    }
  }
};
</script>
