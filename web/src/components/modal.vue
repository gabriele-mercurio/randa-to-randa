<template>
  <v-dialog v-model="dialog" width="500">

    <v-card>
      <v-card-title class="headline grey lighten-2" primary-title>
        Privacy Policy
      </v-card-title>

      <v-card-text>
        
      </v-card-text>

    </v-card>
  </v-dialog>
</template>
<script>
export default {
    props: {
        dialog: {
            type: boolean
        }
    }
}
</script>
