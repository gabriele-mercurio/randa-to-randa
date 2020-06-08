<template>
  <div>
    <v-menu
      ref="menu"
      v-model="menu"
      :close-on-content-click="false"
      :return-value.sync="date"
      transition="scale-transition"
      offset-y
      min-width="290px"
    >
      <template v-slot:activator="{ on }">
        <v-text-field
          v-model="date"
          :label="label"
          prepend-icon="mdi-calendar"
          readonly
          v-on="on"
          :disabled="disabled"
          :error-messages="invalidInterval"
        >
          <template v-slot:append>
            <small class="font-italic font-weight-light">{{
              getPastDateMessage()
            }}</small>
          </template>
        </v-text-field>
      </template>
      <v-date-picker v-model="date" type="month" color="primary" no-title>
        <v-spacer></v-spacer>
        <v-btn text color="primary" @click="menu = false">Annulla</v-btn>
        <v-btn text color="primary" @click="setDate()">OK</v-btn>
      </v-date-picker>
    </v-menu>
  </div>
</template>

<script>
export default {
  data() {
    return {
      label: "",
      menu: false,
      date: null
    };
  },
  props: {
    defLabel: {
      type: String
    },
    defDate: {
      default: null
    },
    disabled: false,
    invalidInterval: "",
    launchType: ""
  },
  methods: {
    setDate() {
      this.$refs.menu.save(this.date);
      this.$emit("setdate", this.date);
      this.menu = false;
    },
    isPastDate() {
      if (this.date) {
        return new Date() > new Date(this.date);
      }
      return false;
    },
    getPastDateMessage() {
      if (this.date) {
        if (this.isPastDate()) {
          return "(" + this.launchType + ")";
        } else {
          return "(previsional)";
        }
      } else {
        return "";
      }
    }
  },
  watch: {
    defDate: {
      handler: function(n, o) {
        this.date = this.defDate;
      },
      deep: true,
      immediate: true
    },
    defLabel: {
      handler: function(n, o) {
        this.label = this.defLabel;
      },
      deep: true,
      immediate: true
    }
  }
};
</script>
