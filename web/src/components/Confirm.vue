<template>
  <v-dialog v-model="dialog" persistent max-width="290">
    <v-card>
      <v-card-title class="headline">Sicuro?</v-card-title>
      <v-card-text>{{ message }}</v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="green darken-1" text @click="disagree()"
          >Annulla</v-btn
        >
        <v-btn color="green darken-1" text @click="agree()"
          >Conferma</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
    data() {
        return {
            dialog: false
        }
    },
    props: {
        message: {
            default:""
        },
        show: {
            default: false
        }
    },
    methods: {
        sendResponse(response) {
            this.$emit("dialogResponse", response);
        },
        agree() {
            this.sendResponse(true);
            this.dialog = false;
        },
        disagree() {
            this.sendResponse(false);
            this.dialog = false;
        }
    },
    watch: {
        show: {
            handler: function(newVal, oldVal) {
                this.dialog = this.show;
            },
            deep: true,
            immediate: true
        }
    }
}
</script>
