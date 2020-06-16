<template>
  <v-snackbar v-model="show" :timeout="timeout" top right>
    <v-icon :color="getColor()">{{ getIcon() }}</v-icon>
    {{ getMessage() }}
    <v-btn color="white" icon @click="show = false">
      <v-icon>mdi-close</v-icon>
    </v-btn>
  </v-snackbar>
</template>

<script>
export default {
  data() {
    return {
      timeout: 3000
    };
  },
  props: {
      show: false,
      data: null
  },
  methods: {
    getMessage() {
      if (this.data) return this.$t(this.data.messageLabel) || "";
      return "";
    },
    getIcon() {
      if (this.data) {
        switch (this.data.state) {
          case "error":
            return "mdi-alert";
          case "success":
            return "mdi-check";
          default:
            return "mdi-check";
        }
      }
      return "";
    },
    getColor() {
      if (this.data) {
        switch (this.data.state) {
          case "error":
            return "red";
          case "success":
            return "green";
          default:
            return "white";
        }
      }
      return null;
    }
  }
};
</script>
